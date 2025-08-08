<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ExerciseController extends Controller
{
    public function show(Lesson $lesson, Exercise $exercise): View
    {
        if ($exercise->lesson_id !== $lesson->id) {
            abort(404);
        }

        return view('exercise', compact('exercise'));
    }

    public function index(): View
    {
        $exercises = Exercise::with('lesson', 'exerciseable')->latest()->paginate(10);
        $lessons = Lesson::orderBy('title')->get();
        return view('superadmin.exercises.index', compact('exercises', 'lessons'));
    }

    public function store(Request $request)
    {
        $content = $request->input('content', []);
        if (isset($content['options'])) {
            foreach ($content['options'] as $index => $option) {
                if (!$request->hasFile("content.options.{$index}.image")) {
                    $content['options'][$index]['image'] = null;
                }
            }
        }
        if (isset($content['pairs'])) {
            foreach ($content['pairs'] as $index => $pair) {
                if (!$request->hasFile("content.pairs.{$index}.item1.image")) {
                    $content['pairs'][$index]['item1']['image'] = null;
                }
                if (!$request->hasFile("content.pairs.{$index}.item1.audio")) {
                    $content['pairs'][$index]['item1']['audio'] = null;
                }
                if (!$request->hasFile("content.pairs.{$index}.item2.image")) {
                    $content['pairs'][$index]['item2']['image'] = null;
                }
                if (!$request->hasFile("content.pairs.{$index}.item2.audio")) {
                    $content['pairs'][$index]['item2']['audio'] = null;
                }
            }
        }
        $request->merge(['content' => $content]);

        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'content' => 'required|array',
            'content.question_text' => 'nullable|string',
            'content.question_word' => 'nullable|string',
            'content.correct_answer' => 'nullable|integer',
            'content.prompt_text' => 'nullable|string',
            'content.sentence' => 'nullable|string',
            'content.instruction' => 'nullable|string',
            'content.sentence_parts' => 'nullable|array',
            'content.correct_answers' => 'nullable|array',
            'content.hints' => 'nullable|string',
            'content.words' => 'nullable|array',
            'content.categories' => 'nullable|array',
            'content.steps' => 'nullable|array',
            'content.options' => 'nullable|array',
            'content.options.*.text' => 'nullable|string',
            'content.options.*.image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'content.audio_file' => ['nullable', 'file', 'mimes:mp3,wav,ogg'],
            'content.pairs' => 'nullable|array',
            'content.pairs.*.item1.text' => 'nullable|string',
            'content.pairs.*.item1.image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'content.pairs.*.item1.audio' => ['nullable', 'file', 'mimes:mp3,wav,ogg'],
            'content.pairs.*.item2.text' => 'nullable|string',
            'content.pairs.*.item2.image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'content.pairs.*.item2.audio' => ['nullable', 'file', 'mimes:mp3,wav,ogg'],
            'content.prompt_image' => ['nullable', 'file', 'image', 'max:2048'],
            'content.prompt_audio' => ['nullable', 'file', 'mimes:mp3,wav,ogg', 'max:5120'],
            'content.question_media' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,mp3,wav,ogg,mp4', 'max:10240'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $modelClass = Relation::getMorphedModel($validated['type']);
            $contentData = $validated['content'];
            $finalOptions = [];

            if (isset($contentData['options'])) {
                foreach ($contentData['options'] as $index => $option) {
                    if ($request->hasFile("content.options.{$index}.image")) {
                        $path = $request->file("content.options.{$index}.image")->store('options', 'public');
                        $finalOptions[] = Storage::url($path);
                    } else {
                        $finalOptions[] = $option['text'] ?? '';
                    }
                }
                $contentData['options'] = $finalOptions;
            }

            if (in_array($validated['type'], ['multiple_choice_quiz', 'translation_match', 'fill_with_options']) && $request->hasFile("content.question_media")) {
                $file = $request->file("content.question_media");
                $path = $file->store('mcq_prompts', 'public');
                $contentData['question_media_url'] = Storage::url($path);

                $mime = $file->getMimeType();
                if (Str::startsWith($mime, 'image/')) {
                    $contentData['question_media_type'] = 'image';
                } elseif (Str::startsWith($mime, 'audio/')) {
                    $contentData['question_media_type'] = 'audio';
                } elseif (Str::startsWith($mime, 'video/')) {
                    $contentData['question_media_type'] = 'video';
                }
            }
            unset($contentData['question_media']);

            if ($validated['type'] === 'matching_game' && isset($contentData['pairs'])) {
                $finalPairs = [];
                foreach ($contentData['pairs'] as $index => $pair) {
                    $item1Value = $this->processPairItem($request, "content.pairs.{$index}.item1", $pair['item1']['text'] ?? '');
                    $item2Value = $this->processPairItem($request, "content.pairs.{$index}.item2", $pair['item2']['text'] ?? '');
                    $finalPairs[] = ['item1' => $item1Value, 'item2' => $item2Value];
                }
                $contentData['pairs'] = $finalPairs;
            }

            if ($validated['type'] === 'speaking_quiz') {
                if ($request->hasFile("content.prompt_image")) {
                    $path = $request->file("content.prompt_image")->store('speaking_prompts', 'public');
                    $contentData['media_url'] = Storage::url($path);
                    $contentData['media_type'] = 'image';
                } elseif ($request->hasFile("content.prompt_audio")) {
                    $path = $request->file("content.prompt_audio")->store('speaking_prompts', 'public');
                    $contentData['media_url'] = Storage::url($path);
                    $contentData['media_type'] = 'audio';
                }
                unset($contentData['prompt_image'], $contentData['prompt_audio']);
            }

            if (isset($contentData['correct_answer'])) {
                $correctIndex = (int)$contentData['correct_answer'];
                if (isset($finalOptions[$correctIndex])) {
                    $contentData['correct_answer'] = $finalOptions[$correctIndex];
                } else {
                    $contentData['correct_answer'] = null;
                }
            }

            if ($modelClass && class_exists($modelClass)) {
                $exerciseDetail = $modelClass::create($contentData);
                $exercise = new Exercise([
                    'lesson_id' => $validated['lesson_id'],
                    'title' => $validated['title'],
                ]);
                $exerciseDetail->exercise()->save($exercise);
            }
        });

        return redirect()->route('superadmin.exercises.index')->with('success', 'Latihan berhasil dibuat.');
    }

    public function update(Request $request, Exercise $exercise)
    {
        $content = $request->input('content', []);
        if (isset($content['options'])) {
            foreach ($content['options'] as $index => $option) {
                if (!$request->hasFile("content.options.{$index}.image")) {
                    $content['options'][$index]['image'] = null;
                }
            }
        }
        if (isset($content['pairs'])) {
            foreach ($content['pairs'] as $index => $pair) {
                if (!$request->hasFile("content.pairs.{$index}.item1.image")) {
                    $content['pairs'][$index]['item1']['image'] = null;
                }
                if (!$request->hasFile("content.pairs.{$index}.item1.audio")) {
                    $content['pairs'][$index]['item1']['audio'] = null;
                }
                if (!$request->hasFile("content.pairs.{$index}.item2.image")) {
                    $content['pairs'][$index]['item2']['image'] = null;
                }
                if (!$request->hasFile("content.pairs.{$index}.item2.audio")) {
                    $content['pairs'][$index]['item2']['audio'] = null;
                }
            }
        }
        $request->merge(['content' => $content]);

        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|string|max:255',
            'content' => 'required|array',
            'content.question_text' => 'nullable|string',
            'content.question_word' => 'nullable|string',
            'content.correct_answer' => 'nullable|integer',
            'content.prompt_text' => 'nullable|string',
            'content.hints' => 'nullable|string',
            'content.sentence' => 'nullable|string',
            'content.instruction' => 'nullable|string',
            'content.sentence_parts' => 'nullable|array',
            'content.correct_answers' => 'nullable|array',
            'content.words' => 'nullable|array',
            'content.categories' => 'nullable|array',
            'content.steps' => 'nullable|array',
            'content.options' => 'nullable|array',
            'content.options.*.text' => 'nullable|string',
            'content.options.*.image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'content.audio_file' => ['nullable', 'file', 'mimes:mp3,wav,ogg'],
            'content.pairs' => 'nullable|array',
            'content.pairs.*.item1.text' => 'nullable|string',
            'content.pairs.*.item1.image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'content.pairs.*.item1.audio' => ['nullable', 'file', 'mimes:mp3,wav,ogg'],
            'content.pairs.*.item2.text' => 'nullable|string',
            'content.pairs.*.item2.image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'content.pairs.*.item2.audio' => ['nullable', 'file', 'mimes:mp3,wav,ogg'],
            'content.prompt_image' => ['nullable', 'file', 'image', 'max:2048'],
            'content.prompt_audio' => ['nullable', 'file', 'mimes:mp3,wav,ogg', 'max:5120'],
            'content.question_media' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,mp3,wav,ogg,mp4', 'max:10240'],
        ]);

        DB::transaction(function () use ($validated, $request, $exercise) {
            $exercise->update([
                'lesson_id' => $validated['lesson_id'],
                'title' => $validated['title'],
            ]);

            $contentData = $validated['content'];
            $exerciseDetail = $exercise->exerciseable;
            $finalOptions = [];

            if ($exerciseDetail && isset($contentData['options'])) {
                $oldOptions = $exerciseDetail->options ?? [];
                foreach ($contentData['options'] as $index => $option) {
                    if ($request->hasFile("content.options.{$index}.image")) {
                        if (isset($oldOptions[$index]) && Str::startsWith($oldOptions[$index], '/storage')) {
                            Storage::disk('public')->delete(str_replace('/storage/', '', $oldOptions[$index]));
                        }
                        $path = $request->file("content.options.{$index}.image")->store('options', 'public');
                        $finalOptions[] = Storage::url($path);
                    } else {
                        $textValue = $option['text'] ?? null;
                        if ($textValue === null && isset($oldOptions[$index]) && Str::startsWith($oldOptions[$index], '/storage')) {
                            $finalOptions[] = $oldOptions[$index];
                        } else {
                            $finalOptions[] = $textValue ?? '';
                        }
                    }
                }
                $contentData['options'] = $finalOptions;
            }

            if ($exercise->exerciseable_type === 'matching_game' && isset($contentData['pairs'])) {
                $finalPairs = [];
                $oldPairs = $exerciseDetail->pairs ?? [];

                foreach ($contentData['pairs'] as $index => $pair) {
                    $oldItem1 = $oldPairs[$index]['item1'] ?? null;
                    $oldItem2 = $oldPairs[$index]['item2'] ?? null;

                    $item1Value = $this->processPairItem($request, "content.pairs.{$index}.item1", $pair['item1']['text'] ?? null, $oldItem1);
                    $item2Value = $this->processPairItem($request, "content.pairs.{$index}.item2", $pair['item2']['text'] ?? null, $oldItem2);

                    $finalPairs[] = ['item1' => $item1Value, 'item2' => $item2Value];
                }
                $contentData['pairs'] = $finalPairs;
            }

            if (in_array($exercise->exerciseable_type, ['multiple_choice_quiz', 'translation_match', 'fill_with_options']) && $request->hasFile("content.question_media")) {
                if ($exerciseDetail->question_media_url) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $exerciseDetail->question_media_url));
                }

                $file = $request->file("content.question_media");
                $path = $file->store('mcq_prompts', 'public');
                $contentData['question_media_url'] = Storage::url($path);

                $mime = $file->getMimeType();
                if (Str::startsWith($mime, 'image/')) {
                    $contentData['question_media_type'] = 'image';
                } elseif (Str::startsWith($mime, 'audio/')) {
                    $contentData['question_media_type'] = 'audio';
                } elseif (Str::startsWith($mime, 'video/')) {
                    $contentData['question_media_type'] = 'video';
                }
            }
            unset($contentData['question_media']);

            if ($exercise->exerciseable_type === 'speaking_quiz') {
                $hasNewImage = $request->hasFile("content.prompt_image");
                $hasNewAudio = $request->hasFile("content.prompt_audio");

                if ($hasNewImage || $hasNewAudio) {
                    if ($exerciseDetail->media_url) {
                        Storage::disk('public')->delete(str_replace('/storage/', '', $exerciseDetail->media_url));
                    }

                    if ($hasNewImage) {
                        $path = $request->file("content.prompt_image")->store('speaking_prompts', 'public');
                        $contentData['media_url'] = Storage::url($path);
                        $contentData['media_type'] = 'image';
                    } elseif ($hasNewAudio) {
                        $path = $request->file("content.prompt_audio")->store('speaking_prompts', 'public');
                        $contentData['media_url'] = Storage::url($path);
                        $contentData['media_type'] = 'audio';
                    }
                }
                unset($contentData['prompt_image'], $contentData['prompt_audio']);
            }

            if (isset($contentData['correct_answer'])) {
                $correctIndex = (int)$contentData['correct_answer'];
                $optionsToUse = !empty($finalOptions) ? $finalOptions : ($exerciseDetail->options ?? []);
                if (isset($optionsToUse[$correctIndex])) {
                    $contentData['correct_answer'] = $optionsToUse[$correctIndex];
                } else {
                    $contentData['correct_answer'] = null;
                }
            }

            if ($exerciseDetail) {
                if ($exercise->exerciseable_type === 'spelling_quiz' && $request->hasFile('content.audio_file')) {
                    if ($exerciseDetail->audio_url) {
                        Storage::disk('public')->delete(str_replace('/storage/', '', $exerciseDetail->audio_url));
                    }
                    $path = $request->file('content.audio_file')->store('audio', 'public');
                    $contentData['audio_url'] = Storage::url($path);
                }
                unset($contentData['audio_file']);

                $exerciseDetail->update($contentData);
            }
        });

        return redirect()->route('superadmin.exercises.index')->with('success', 'Latihan berhasil diperbarui.');
    }

    public function destroy(Exercise $exercise)
    {
        DB::transaction(function () use ($exercise) {
            if ($exercise->exerciseable) {
                if (isset($exercise->exerciseable->audio_url)) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $exercise->exerciseable->audio_url));
                }
                if (isset($exercise->exerciseable->options)) {
                    foreach ($exercise->exerciseable->options as $option) {
                        if (Str::startsWith($option, '/storage')) {
                            Storage::disk('public')->delete(str_replace('/storage/', '', $option));
                        }
                    }
                }
                $exercise->exerciseable->delete();
            }
            $exercise->delete();
        });

        return redirect()->route('superadmin.exercises.index')->with('success', 'Latihan berhasil dihapus.');
    }

    private function processPairItem(Request $request, string $baseName, ?string $textValue, ?string $oldValue = null): string
    {
        if ($request->hasFile("{$baseName}.image")) {
            if ($oldValue && Str::startsWith($oldValue, '/storage')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $oldValue));
            }
            $path = $request->file("{$baseName}.image")->store('pairs', 'public');
            return Storage::url($path);
        }

        if ($request->hasFile("{$baseName}.audio")) {
            if ($oldValue && Str::startsWith($oldValue, '/storage')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $oldValue));
            }
            $path = $request->file("{$baseName}.audio")->store('pairs', 'public');
            return Storage::url($path);
        }

        if ($textValue === null && $oldValue && Str::startsWith($oldValue, '/storage')) {
            return $oldValue;
        }

        return $textValue ?? '';
    }
}
