<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Vocabulary;
use App\Models\VocabularyItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VocabularySeeder extends Seeder
{
    public function run()
    {
        // 1. Kosongkan semua tabel terkait untuk memastikan data bersih
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Module::truncate();
        Lesson::truncate();
        Vocabulary::truncate();
        VocabularyItem::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Definisikan semua data kursus dalam satu array besar
        $courseData = [
            [
                'module_title' => 'Pre-Learning',
                'module_description' => 'Modul pengantar untuk membiasakan diri dengan dasar-dasar bahasa Inggris yang paling fundamental.',
                'lessons' => [
                    [
                        'lesson_title' => 'English Alphabet and Pronunciation Basics',
                        'vocabularies' => [
                            ['category' => 'Alphabet', 'term' => 'A-Z with sounds', 'details' => '(e.g., "A" /eɪ/, "B" /biː/, "C" /siː/, "Z" /ziː/).'],
                            ['category' => 'Sound Contrasts', 'term' => '"th"', 'details' => '(/θ/ in "think", /ð/ in "this") – uncommon in Bahasa Indonesia.'],
                            ['category' => 'Sound Contrasts', 'term' => '"r"', 'details' => '(/r/ in "room") vs. Indonesian rolled "r".'],
                            ['category' => 'Sound Contrasts', 'term' => '"l"', 'details' => '(/l/ in "local") vs. "r" confusion.'],
                            ['category' => 'Sound Contrasts', 'term' => '"v"', 'details' => '(/v/ in "visit") vs. "w" (/w/ in "welcome").'],
                            ['category' => 'Silent Letters', 'term' => '"knife"', 'details' => '/naɪf/'],
                            ['category' => 'Silent Letters', 'term' => '"lamb"', 'details' => '/læm/'],
                            ['category' => 'Silent Letters', 'term' => '"write"', 'details' => '/raɪt/'],
                            ['category' => 'Silent Letters', 'term' => '"hour"', 'details' => '/aʊər/'],
                            ['category' => 'Hospitality Words: Basic', 'term' => 'hotel', 'details' => '/hoʊˈtɛl/'],
                            ['category' => 'Hospitality Words: Basic', 'term' => 'guest', 'details' => '/ɡɛst/'],
                            ['category' => 'Hospitality Words: Basic', 'term' => 'room', 'details' => '/ruːm/'],
                            ['category' => 'Hospitality Words: Basic', 'term' => 'key', 'details' => '/kiː/'],
                            ['category' => 'Hospitality Words: Basic', 'term' => 'reservation', 'details' => '/rɛzərˈveɪʃn/'],
                            ['category' => 'Hospitality Words: Staff-related', 'term' => 'reception', 'details' => '/rɪˈsɛpʃn/'],
                            ['category' => 'Hospitality Words: Staff-related', 'term' => 'lobby', 'details' => '/lɑːbi/'],
                            ['category' => 'Hospitality Words: Staff-related', 'term' => 'concierge', 'details' => '/kɑːn.siˈɛrʒ/'],
                            ['category' => 'Hospitality Words: Guest-related', 'term' => 'suite', 'details' => '/swiːt/'],
                            ['category' => 'Hospitality Words: Guest-related', 'term' => 'amenities', 'details' => '/əˈmɛnɪtiz/'],
                            ['category' => 'Hospitality Words: Guest-related', 'term' => 'buffet', 'details' => '/ˈbʊfeɪ/'],
                            ['category' => 'Hospitality Words: Guest-related', 'term' => 'check-in', 'details' => '/ˈtʃɛk ɪn/'],
                            ['category' => 'Hospitality Words: Guest-related', 'term' => 'check-out', 'details' => '/ˈtʃɛk aʊt/'],
                            ['category' => 'Hospitality Words: New Additions', 'term' => 'balcony', 'details' => '/ˈbælkəni/'],
                            ['category' => 'Hospitality Words: New Additions', 'term' => 'minibar', 'details' => '/ˈmɪniˌbɑːr/'],
                            ['category' => 'Hospitality Words: New Additions', 'term' => 'elevator', 'details' => '/ˈɛlɪveɪtər/'],
                            ['category' => 'Hospitality Words: New Additions', 'term' => 'luggage', 'details' => '/ˈlʌɡɪdʒ/'],
                            ['category' => 'Hospitality Words: New Additions', 'term' => 'thank you', 'details' => '/θæŋk juː/'],
                            ['category' => 'Hospitality Words: New Additions', 'term' => 'available', 'details' => '/əˈveɪləbl/'],
                            ['category' => 'Hospitality Words: New Additions', 'term' => 'vacant', 'details' => '/ˈveɪkənt/'],
                        ]
                    ],
                    [
                        'lesson_title' => 'Essential Greetings and Self-Introductions',
                        'vocabularies' => [
                            ['category' => 'Greetings', 'term' => 'Hello', 'details' => 'Halo'],
                            ['category' => 'Greetings', 'term' => 'Hi', 'details' => 'Hai'],
                            ['category' => 'Greetings', 'term' => 'Good morning', 'details' => 'Selamat pagi'],
                            ['category' => 'Greetings', 'term' => 'Good afternoon', 'details' => 'Selamat siang'],
                            ['category' => 'Greetings', 'term' => 'Good evening', 'details' => 'Selamat malam'],
                            ['category' => 'Greetings', 'term' => 'Welcome', 'details' => 'Selamat datang'],
                            ['category' => 'Greetings', 'term' => 'Nice to meet you', 'details' => 'Senang bertemu dengan Anda'],
                            ['category' => 'Introductions', 'term' => 'My name is [Name]', 'details' => 'Nama saya [Name]'],
                            ['category' => 'Introductions', 'term' => 'I’m [Name]', 'details' => 'Saya [Name])'],
                            ['category' => 'Introductions', 'term' => 'I’m your receptionist', 'details' => 'Saya resepsionis Anda'],
                            ['category' => 'Introductions', 'term' => 'I work at Youkata Stay Hotel', 'details' => 'Saya bekerja di Youkata Stay Hotel'],
                            ['category' => 'Introductions', 'term' => 'I’m here to assist you', 'details' => 'Saya di sini untuk membantu Anda'],
                            ['category' => 'Responses', 'term' => 'Thank you', 'details' => 'Terima kasih'],
                            ['category' => 'Responses', 'term' => 'You’re welcome', 'details' => 'Sama-sama'],
                            ['category' => 'Responses', 'term' => 'How are you?', 'details' => 'Apa kabar?'],
                            ['category' => 'Responses', 'term' => 'I’m fine, thank you', 'details' => 'Saya baik, terima kasih'],
                            ['category' => 'New Additions', 'term' => 'Good night', 'details' => 'Selamat malam'],
                            ['category' => 'New Additions', 'term' => 'Have a pleasant stay', 'details' => 'Selamat menikmati masa tinggal Anda'],
                            ['category' => 'New Additions', 'term' => 'I’m the front desk manager', 'details' => 'Saya manajer meja depan'],
                            ['category' => 'New Additions', 'term' => 'Please let me know if you need help', 'details' => 'Tolong beri tahu saya jika Anda butuh bantuan'],
                        ]
                    ],
                    [
                        'lesson_title' => 'Advanced Pronunciation Practice',
                        'vocabularies' => [
                            ['category' => 'Minimal Pairs', 'term' => '"ship" vs. "sheep"', 'details' => '/ʃɪp/ vs. /ʃiːp/'],
                            ['category' => 'Minimal Pairs', 'term' => '"bat" vs. "bet"', 'details' => '/bæt/ vs. /bɛt/'],
                            ['category' => 'Minimal Pairs', 'term' => '"pen" vs. "pin"', 'details' => '/pɛn/ vs. /pɪn/'],
                            ['category' => 'Minimal Pairs', 'term' => '"fan" vs. "van"', 'details' => '/fæn/ vs. /væn/'],
                            ['category' => 'Minimal Pairs', 'term' => '"rice" vs. "lice"', 'details' => '/raɪs/ vs. /laɪs/'],
                            ['category' => 'Hospitality Terms', 'term' => 'pool', 'details' => '/puːl/'],
                            ['category' => 'Hospitality Terms', 'term' => 'tour', 'details' => '/tʊr/'],
                            ['category' => 'Hospitality Terms', 'term' => 'floor', 'details' => '/flɔːr/'],
                            ['category' => 'Hospitality Terms', 'term' => 'bill', 'details' => '/bɪl/'],
                            ['category' => 'Hospitality Terms', 'term' => 'meal', 'details' => '/miːl/'],
                            ['category' => 'Hospitality Terms', 'term' => 'towel', 'details' => '/ˈtaʊəl/'],
                            ['category' => 'Hospitality Terms', 'term' => 'shower', 'details' => '/ˈʃaʊər/'],
                            ['category' => 'Hospitality Terms', 'term' => 'luggage', 'details' => '/ˈlʌɡɪdʒ/'],
                            ['category' => 'Hospitality Terms', 'term' => 'elevator', 'details' => '/ˈɛlɪveɪtər/'],
                            ['category' => 'Hospitality Terms', 'term' => 'parking', 'details' => '/ˈpɑːrkɪŋ/'],
                            ['category' => 'New Additions', 'term' => '"suite" vs. "sweet"', 'details' => '/swiːt/ vs. /swiːt/'],
                            ['category' => 'New Additions', 'term' => '"room" vs. "loom"', 'details' => '/ruːm/ vs. /luːm/'],
                            ['category' => 'New Additions', 'term' => '"bill" vs. "bell"', 'details' => '/bɪl/ vs. /bɛl/'],
                            ['category' => 'New Additions', 'term' => 'receipt', 'details' => '/rɪˈsiːt/'],
                            ['category' => 'New Additions', 'term' => 'itinerary', 'details' => '/aɪˈtɪnərɛri/'],
                        ]
                    ],
                    [
                        'lesson_title' => 'Present Simple Grammar',
                        'vocabularies' => [
                            ['category' => 'Verbs', 'term' => 'work', 'details' => 'bekerja'],
                            ['category' => 'Verbs', 'term' => 'help', 'details' => 'membantu'],
                            ['category' => 'Verbs', 'term' => 'provide', 'details' => 'menyediakan'],
                            ['category' => 'Verbs', 'term' => 'offer', 'details' => 'menawarkan'],
                            ['category' => 'Verbs', 'term' => 'stay', 'details' => 'menginap'],
                            ['category' => 'Verbs', 'term' => 'need', 'details' => 'membutuhkan'],
                            ['category' => 'Verbs', 'term' => 'speak', 'details' => 'berbicara'],
                            ['category' => 'Sentences', 'term' => 'I work at the hotel', 'details' => 'Saya bekerja di hotel'],
                            ['category' => 'Sentences', 'term' => 'You help the guests', 'details' => 'Anda membantu tamu'],
                            ['category' => 'Sentences', 'term' => 'We provide free Wi-Fi', 'details' => 'Kami menyediakan Wi-Fi gratis'],
                            ['category' => 'Sentences', 'term' => 'They offer breakfast', 'details' => 'Mereka menawarkan sarapan'],
                            ['category' => 'Sentences', 'term' => 'The guest stays for two nights', 'details' => 'Tamu menginap dua malam'],
                            ['category' => 'New Additions', 'term' => 'I clean the rooms', 'details' => 'Saya membersihkan kamar'],
                            ['category' => 'New Additions', 'term' => 'We serve dinner', 'details' => 'Kami menyajikan makan malam'],
                            ['category' => 'New Additions', 'term' => 'The hotel has a pool', 'details' => 'Hotel punya kolam renang'],
                        ]
                    ],
                    [
                        'lesson_title' => 'Simple Past Grammar',
                        'vocabularies' => [
                            ['category' => 'Verbs', 'term' => 'checked', 'details' => 'memeriksa'],
                            ['category' => 'Verbs', 'term' => 'stayed', 'details' => 'menginap'],
                            ['category' => 'Verbs', 'term' => 'arrived', 'details' => 'tiba'],
                            ['category' => 'Verbs', 'term' => 'booked', 'details' => 'memesan'],
                            ['category' => 'Verbs', 'term' => 'asked', 'details' => 'bertanya'],
                            ['category' => 'Verbs', 'term' => 'paid', 'details' => 'membayar'],
                            ['category' => 'Verbs', 'term' => 'left', 'details' => 'meninggalkan'],
                            ['category' => 'Sentences', 'term' => 'The guest checked in yesterday', 'details' => 'Tamu check-in kemarin'],
                            ['category' => 'Sentences', 'term' => 'You stayed for three days', 'details' => 'Anda menginap tiga hari'],
                            ['category' => 'Sentences', 'term' => 'She arrived this morning', 'details' => 'Dia tiba pagi ini'],
                            ['category' => 'Sentences', 'term' => 'We booked a room', 'details' => 'Kami memesan kamar'],
                            ['category' => 'New Additions', 'term' => 'The guest requested a towel', 'details' => 'Tamu meminta handuk'],
                            ['category' => 'New Additions', 'term' => 'I cleaned the room', 'details' => 'Saya membersihkan kamar'],
                        ]
                    ],
                    [
                        'lesson_title' => 'Numbers and Time',
                        'vocabularies' => [
                            ['category' => 'Numbers', 'term' => '1-100', 'details' => '(e.g., "one" /wʌn/, "ten" /tɛn/, "fifty" /ˈfɪfti/, "hundred" /ˈhʌndrəd/)'],
                            ['category' => 'Time', 'term' => '8:00', 'details' => 'eight o’clock'],
                            ['category' => 'Time', 'term' => '9:30', 'details' => 'nine thirty'],
                            ['category' => 'Time', 'term' => '11:45', 'details' => 'quarter to twelve'],
                            ['category' => 'Time', 'term' => 'noon', 'details' => 'tengah hari'],
                            ['category' => 'Time', 'term' => 'midnight', 'details' => 'tengah malam'],
                            ['category' => 'Phrases', 'term' => 'Check-in is at 2 PM', 'details' => 'Check-in pukul 2 siang'],
                            ['category' => 'Phrases', 'term' => 'Breakfast starts at 7 AM', 'details' => 'Sarapan mulai pukul 7 pagi'],
                            ['category' => 'Phrases', 'term' => 'The shuttle leaves at 10:15', 'details' => 'Shuttle berangkat pukul 10:15'],
                            ['category' => 'New Additions', 'term' => 'Your room is number 205', 'details' => 'Kamar Anda nomor 205'],
                            ['category' => 'New Additions', 'term' => 'Dinner is at 6:30 PM', 'details' => 'Makan malam pukul 6:30 malam'],
                        ]
                    ],
                    [
                        'lesson_title' => 'Basic Questions and Responses',
                        'vocabularies' => [
                            ['category' => 'Questions', 'term' => 'What’s your name?', 'details' => 'Siapa nama Anda?'],
                            ['category' => 'Questions', 'term' => 'Where are you from?', 'details' => 'Dari mana Anda?'],
                            ['category' => 'Questions', 'term' => 'Do you have a reservation?', 'details' => 'Apakah Anda punya reservasi?'],
                            ['category' => 'Questions', 'term' => 'How can I help you?', 'details' => 'Bagaimana saya bisa membantu?'],
                            ['category' => 'Questions', 'term' => 'When did you arrive?', 'details' => 'Kapan Anda tiba?'],
                            ['category' => 'Responses', 'term' => 'My name is Budi', 'details' => 'Nama saya Budi'],
                            ['category' => 'Responses', 'term' => 'I’m from Jakarta', 'details' => 'Saya dari Jakarta'],
                            ['category' => 'Responses', 'term' => 'Yes, I do', 'details' => 'Ya, saya punya'],
                            ['category' => 'Responses', 'term' => 'No, I don’t', 'details' => 'Tidak, saya tidak punya'],
                            ['category' => 'Responses', 'term' => 'I arrived today', 'details' => 'Saya tiba hari ini'],
                            ['category' => 'New Additions', 'term' => 'How many nights will you stay?', 'details' => 'Berapa malam Anda menginap?'],
                            ['category' => 'New Additions', 'term' => 'Would you like a non-smoking room?', 'details' => 'Apakah Anda ingin kamar bebas rokok?'],
                        ]
                    ],
                    [
                        'lesson_title' => 'Cultural Tips and Communication Skills',
                        'vocabularies' => [
                            ['category' => 'Politeness', 'term' => 'Please', 'details' => 'Tolong'],
                            ['category' => 'Politeness', 'term' => 'Thank you', 'details' => 'Terima kasih'],
                            ['category' => 'Politeness', 'term' => 'Sorry', 'details' => 'Maaf'],
                            ['category' => 'Politeness', 'term' => 'Excuse me', 'details' => 'Permisi'],
                            ['category' => 'Politeness', 'term' => 'May I?', 'details' => 'Boleh saya?'],
                            ['category' => 'Comfort Phrases', 'term' => 'Take your time', 'details' => 'Ambil waktu Anda'],
                            ['category' => 'Comfort Phrases', 'term' => 'No problem', 'details' => 'Tidak masalah'],
                            ['category' => 'Comfort Phrases', 'term' => 'I’ll check for you', 'details' => 'Saya akan cek untuk Anda'],
                            ['category' => 'New Additions', 'term' => 'Would you like help with your luggage?', 'details' => 'Apakah Anda butuh bantuan dengan bagasi?'],
                            ['category' => 'New Additions', 'term' => 'Let me know if you need anything', 'details' => 'Beri tahu saya jika Anda butuh sesuatu'],
                        ]
                    ]
                ]
            ],
            [
                'module_title' => 'Greetings and Basic Interactions',
                'module_description' => 'Modul untuk melatih salam dan interaksi dasar dengan tamu.',
                'lessons' => [
                    [
                        'lesson_title' => 'Greetings and Welcoming Guests',
                        'vocabularies' => [
                            ['category' => 'Greetings', 'term' => 'Welcome to Youkata Stay Hotel!', 'details' => '(Selamat datang di Youkata Stay Hotel!)'],
                            ['category' => 'Greetings', 'term' => 'Good morning, sir/madam', 'details' => '(Selamat pagi, tuan/nyonya)'],
                            ['category' => 'Greetings', 'term' => 'Hello, how are you today?', 'details' => '(Halo, apa kabar Anda hari ini?)'],
                            ['category' => 'Greetings', 'term' => 'It’s a pleasure to see you', 'details' => '(Senang bertemu Anda)'],
                            ['category' => 'Greetings', 'term' => 'Welcome back!', 'details' => '(Selamat datang kembali!)'],
                            ['category' => 'Offers', 'term' => 'How may I assist you today?', 'details' => '(Bagaimana saya bisa membantu Anda hari ini?)'],
                            ['category' => 'Offers', 'term' => 'Can I help you with anything?', 'details' => '(Bisa saya bantu apa saja?)'],
                            ['category' => 'Offers', 'term' => 'Would you like some help?', 'details' => '(Apakah Anda ingin bantuan?)'],
                            ['category' => 'Offers', 'term' => 'Let me assist you', 'details' => '(Izinkan saya membantu Anda)'],
                            ['category' => 'Responses', 'term' => 'Enjoy your stay!', 'details' => '(Selamat menikmati masa tinggal Anda!)'],
                            ['category' => 'Responses', 'term' => 'Have a great day!', 'details' => '(Semoga hari Anda menyenangkan!)'],
                            ['category' => 'Responses', 'term' => 'See you later!', 'details' => '(Sampai jumpa nanti!)'],
                            ['category' => 'Responses', 'term' => 'Take care!', 'details' => '(Hati-hati!)'],
                            ['category' => 'New Additions', 'term' => 'We’re happy to have you here', 'details' => '(Kami senang Anda di sini)'],
                            ['category' => 'New Additions', 'term' => 'Good afternoon, how can I assist?', 'details' => '(Selamat siang, bagaimana saya bisa membantu?)'],
                        ]
                    ],
                    [
                        'lesson_title' => 'Basic Interactions and Politeness',
                        'vocabularies' => [
                            ['category' => 'Inquiries', 'term' => 'Do you have a reservation?', 'details' => '(Apakah Anda punya reservasi?)'],
                            ['category' => 'Inquiries', 'term' => 'May I have your name, please?', 'details' => '(Boleh saya tahu nama Anda?)'],
                            ['category' => 'Inquiries', 'term' => 'Are you checking in today?', 'details' => '(Apakah Anda check-in hari ini?)'],
                            ['category' => 'Inquiries', 'term' => 'What time will you arrive?', 'details' => '(Jam berapa Anda tiba?)'],
                            ['category' => 'Politeness', 'term' => 'Please wait a moment', 'details' => '(Tolong tunggu sebentar)'],
                            ['category' => 'Politeness', 'term' => 'Thank you for waiting', 'details' => '(Terima kasih telah menunggu)'],
                            ['category' => 'Politeness', 'term' => 'I’m sorry for the delay', 'details' => '(Maaf atas keterlambatannya)'],
                            ['category' => 'Politeness', 'term' => 'You’re very welcome', 'details' => '(Sama-sama)'],
                            ['category' => 'Politeness', 'term' => 'Excuse me, sir/madam', 'details' => '(Permisi, tuan/nyonya)'],
                            ['category' => 'Farewells', 'term' => 'Thank you for choosing us', 'details' => '(Terima kasih telah memilih kami)'],
                            ['category' => 'Farewells', 'term' => 'Have a safe trip!', 'details' => '(Selamat jalan!)'],
                            ['category' => 'Farewells', 'term' => 'We hope to see you again', 'details' => '(Kami harap bertemu Anda lagi)'],
                            ['category' => 'Farewells', 'term' => 'Goodbye', 'details' => '(Selamat tinggal)'],
                            ['category' => 'New Additions', 'term' => 'Is there anything else I can do?', 'details' => '(Apakah ada lagi yang bisa saya lakukan?)'],
                            ['category' => 'New Additions', 'term' => 'We appreciate your patience', 'details' => '(Kami menghargai kesabaran Anda)'],
                        ]
                    ]
                ]
            ],
            [
                'module_title' => 'Check-In Procedures',
                'module_description' => 'Prosedur standar untuk proses check-in tamu hotel.',
                'lessons' => [
                    [
                        'lesson_title' => 'Verifying Reservations and Collecting Information',
                        'vocabularies' => [
                            ['category' => 'Verification', 'term' => 'May I see your reservation confirmation?', 'details' => '(Boleh saya lihat konfirmasi reservasi Anda?)'],
                            ['category' => 'Verification', 'term' => 'Do you have a booking under your name?', 'details' => '(Apakah Anda punya pemesanan atas nama Anda?)'],
                            ['category' => 'Verification', 'term' => 'Can you tell me your booking number?', 'details' => '(Bisa beri tahu nomor pemesanan Anda?)'],
                            ['category' => 'Verification', 'term' => 'I’ll check your reservation', 'details' => '(Saya akan cek reservasi Anda)'],
                            ['category' => 'Information', 'term' => 'Could you please show me your ID?', 'details' => '(Bisa tunjukkan ID Anda?)'],
                            ['category' => 'Information', 'term' => 'May I have your passport, please?', 'details' => '(Boleh saya lihat paspor Anda?)'],
                            ['category' => 'Information', 'term' => 'We need your credit card for incidentals', 'details' => '(Kami butuh kartu kredit Anda untuk biaya tambahan)'],
                            ['category' => 'Information', 'term' => 'Please sign here', 'details' => '(Tolong tanda tangan di sini)'],
                            ['category' => 'Confirmation', 'term' => 'Your reservation is confirmed', 'details' => '(Reservasi Anda sudah dikonfirmasi)'],
                            ['category' => 'Confirmation', 'term' => 'Everything is ready for you', 'details' => '(Semuanya sudah siap untuk Anda)'],
                            ['category' => 'Confirmation', 'term' => 'You’re all set!', 'details' => '(Anda sudah selesai!)'],
                            ['category' => 'New Additions', 'term' => 'We require a deposit of IDR 500,000', 'details' => '(Kami memerlukan deposit IDR 500,000)'],
                            ['category' => 'New Additions', 'term' => 'Please fill out this form', 'details' => '(Tolong isi formulir ini)'],
                        ]
                    ],
                    [
                        'lesson_title' => 'Explaining Policies and Handling Requests',
                        'vocabularies' => [
                            ['category' => 'Policies', 'term' => 'Check-in time is at 2 PM', 'details' => '(Waktu check-in pukul 2 siang)'],
                            ['category' => 'Policies', 'term' => 'Check-out is at 12 PM', 'details' => '(Check-out pukul 12 siang)'],
                            ['category' => 'Policies', 'term' => 'Breakfast is from 7 AM to 10 AM', 'details' => '(Sarapan dari pukul 7 sampai 10 pagi)'],
                            ['category' => 'Policies', 'term' => 'Wi-Fi is free', 'details' => '(Wi-Fi gratis)'],
                            ['category' => 'Policies', 'term' => 'Smoking is not allowed', 'details' => '(Dilarang merokok)'],
                            ['category' => 'Room Info', 'term' => 'Your room is number 305', 'details' => '(Kamar Anda nomor 305)'],
                            ['category' => 'Room Info', 'term' => 'It’s on the third floor', 'details' => '(Ada di lantai tiga)'],
                            ['category' => 'Room Info', 'term' => 'Here are your keys', 'details' => '(Ini kunci Anda)'],
                            ['category' => 'Room Info', 'term' => 'The elevator is to your left', 'details' => '(Lift di sebelah kiri)'],
                            ['category' => 'Requests', 'term' => 'Would you like a late check-out?', 'details' => '(Apakah Anda ingin check-out terlambat?)'],
                            ['category' => 'Requests', 'term' => 'Do you need extra towels?', 'details' => '(Apakah Anda butuh handuk tambahan?)'],
                            ['category' => 'Requests', 'term' => 'Can I arrange a wake-up call?', 'details' => '(Bisa saya atur panggilan bangun?)'],
                            ['category' => 'Requests', 'term' => 'I’ll send someone to help', 'details' => '(Saya akan kirim seseorang)'],
                            ['category' => 'New Additions', 'term' => 'Pets are not allowed', 'details' => '(Hewan peliharaan dilarang)'],
                            ['category' => 'New Additions', 'term' => 'Quiet hours are from 10 PM', 'details' => '(Jam tenang mulai pukul 10 malam)'],
                        ]
                    ]
                ]
            ],
            [
                'module_title' => 'Hotel Amenities and Services',
                'module_description' => 'Mengenal fasilitas dan layanan hotel serta cara merekomendasikannya.',
                'lessons' => [
                    [
                        'lesson_title' => 'Describing Hotel Amenities',
                        'vocabularies' => [
                            ['category' => 'Amenities', 'term' => 'Wi-Fi', 'details' => 'Wi-Fi'],
                            ['category' => 'Amenities', 'term' => 'breakfast', 'details' => 'sarapan'],
                            ['category' => 'Amenities', 'term' => 'quick bites', 'details' => 'makanan ringan'],
                            ['category' => 'Amenities: Expanded', 'term' => 'coffee', 'details' => 'kopi'],
                            ['category' => 'Describing Amenities', 'term' => 'We offer free Wi-Fi throughout the hotel.', 'details' => 'Kami menawarkan Wi-Fi gratis di seluruh hotel.'],
                            ['category' => 'Describing Amenities: Expanded', 'term' => 'Each room comes with toiletries and a hairdryer.', 'details' => 'Setiap kamar dilengkapi dengan perlengkapan mandi dan pengering rambut.'],
                        ]
                    ],
                    [
                        'lesson_title' => 'Promoting Services and Answering Inquiries',
                        'vocabularies' => [
                            ['category' => 'Services', 'term' => 'laundry services', 'details' => 'layanan laundry'],
                            ['category' => 'Services: Expanded', 'term' => 'airport shuttle', 'details' => 'antar-jemput bandara'],
                            ['category' => 'Answering Inquiries', 'term' => 'Yes, we have instant noodles available 24/7.', 'details' => 'Ya, kami menyediakan mie instan 24 jam.'],
                            ['category' => 'Promoting Services', 'term' => 'Would you like to book a Bromo tour? It’s very popular!', 'details' => 'Apakah Anda ingin memesan tur Bromo? Ini sangat populer!'],
                        ]
                    ],
                    [
                        'lesson_title' => 'Giving Directions Inside the Hotel',
                        'vocabularies' => [
                            ['category' => 'Directions', 'term' => 'left', 'details' => 'kiri'],
                            ['category' => 'Directions: Expanded', 'term' => 'opposite', 'details' => 'di seberang'],
                            ['category' => 'Places', 'term' => 'lobby', 'details' => 'lobi'],
                            ['category' => 'Giving Directions', 'term' => 'The elevator is next to the front desk.', 'details' => 'Lift ada di sebelah meja depan.'],
                        ]
                    ],
                    [
                        'lesson_title' => 'Recommending Local Attractions',
                        'vocabularies' => [
                            ['category' => 'Attractions', 'term' => 'Bromo Tengger Semeru National Park', 'details' => 'Taman Nasional Bromo Tengger Semeru'],
                            ['category' => 'Recommendations', 'term' => 'I recommend the Malang Night Market for local food.', 'details' => 'Saya merekomendasikan Pasar Malam Malang untuk makanan lokal.'],
                            ['category' => 'Distance/Time', 'term' => 'It’s about 10 minutes by car.', 'details' => 'Sekitar 10 menit dengan mobil.'],
                        ]
                    ],
                ]
            ],
            [
                'module_title' => 'Handling Guest Requests and Issues',
                'module_description' => 'Mempelajari cara menangani permintaan dan keluhan umum dari tamu.',
                'lessons' => [
                    [
                        'lesson_title' => 'Handling Common Guest Requests',
                        'vocabularies' => [
                            ['category' => 'Requests', 'term' => 'Can I have extra towels, please?', 'details' => 'Bisa minta handuk tambahan?'],
                            ['category' => 'Requests', 'term' => 'Please arrange a wake-up call for 6 AM.', 'details' => 'Tolong atur panggilan bangun pukul 6 pagi.'],
                            ['category' => 'Requests: Expanded', 'term' => 'I’d like room service.', 'details' => 'Saya ingin pesan layanan kamar.'],
                            ['category' => 'Requests: Expanded', 'term' => 'Can I check out late?', 'details' => 'Bisa check-out terlambat?'],
                            ['category' => 'Responses', 'term' => 'I’ll bring extra towels right away.', 'details' => 'Saya akan segera membawa handuk tambahan.'],
                            ['category' => 'Responses', 'term' => 'I’ve set your wake-up call for 6 AM.', 'details' => 'Panggilan bangun Anda sudah diatur pukul 6 pagi.'],
                            ['category' => 'Responses: Expanded', 'term' => 'Room service will arrive in 30 minutes.', 'details' => 'Layanan kamar akan sampai dalam 30 menit.'],
                        ]
                    ],
                    [
                        'lesson_title' => 'Managing Guest Complaints',
                        'vocabularies' => [
                            ['category' => 'Complaints', 'term' => 'The room is too noisy.', 'details' => 'Kamar terlalu berisik.'],
                            ['category' => 'Complaints', 'term' => 'The Wi-Fi isn’t working.', 'details' => 'Wi-Fi tidak berfungsi.'],
                            ['category' => 'Complaints: Expanded', 'term' => 'The AC isn’t cold.', 'details' => 'AC tidak dingin.'],
                            ['category' => 'Complaints: Expanded', 'term' => 'No hot water.', 'details' => 'Tidak ada air panas.'],
                            ['category' => 'Responses', 'term' => 'I’m very sorry for the inconvenience.', 'details' => 'Maaf atas ketidaknyamanannya.'],
                            ['category' => 'Responses', 'term' => 'Would you like to move to a quieter room?', 'details' => 'Apakah Anda ingin pindah ke kamar yang lebih tenang?'],
                            ['category' => 'Responses: Expanded', 'term' => 'I’ll have maintenance check on it immediately.', 'details' => 'Saya akan meminta bagian pemeliharaan untuk segera memeriksanya.'],
                        ]
                    ],
                ]
            ],
            [
                'module_title' => 'Check-Out Procedures',
                'module_description' => 'Mempelajari alur check-out dan pembayaran.',
                'lessons' => [
                    [
                        'lesson_title' => 'Processing Check-Outs and Explaining Bills',
                        'vocabularies' => [
                            ['category' => 'Billing', 'term' => 'Your total is 200,000 rupiah, including tax.', 'details' => 'Total Anda 200.000 rupiah, sudah termasuk pajak.'],
                            ['category' => 'Billing', 'term' => 'The room rate is 150,000 rupiah per night.', 'details' => 'Tarif kamar 150.000 rupiah per malam.'],
                            ['category' => 'Payment Options', 'term' => 'We accept cash, credit cards, or mobile payments like GoPay.', 'details' => 'Kami menerima tunai, kartu kredit, atau pembayaran mobile seperti GoPay.'],
                            ['category' => 'Receipts', 'term' => 'Here’s your receipt.', 'details' => 'Ini tanda terima Anda.'],
                        ]
                    ],
                    [
                        'lesson_title' => 'Handling Payments and Special Requests',
                        'vocabularies' => [
                            ['category' => 'Payment Handling', 'term' => 'Would you like to split the payment?', 'details' => 'Apakah Anda ingin membagi pembayaran?'],
                            ['category' => 'Payment Handling', 'term' => 'Your payment is confirmed. Thank you!', 'details' => 'Pembayaran Anda sudah dikonfirmasi. Terima kasih!'],
                            ['category' => 'Special Requests', 'term' => 'We can store your luggage until 6 PM.', 'details' => 'Kami bisa simpan bagasi Anda hingga pukul 6 sore.'],
                            ['category' => 'Special Requests', 'term' => 'Did you lose something? Let me check our lost and found.', 'details' => 'Apakah Anda kehilangan sesuatu? Saya cek di barang hilang.'],
                        ]
                    ],
                ]
            ],
            [
                'module_title' => 'Telephone Etiquette',
                'module_description' => 'Etiket dan frasa standar saat berkomunikasi melalui telepon.',
                'lessons' => [
                    [
                        'lesson_title' => 'Greeting Callers and Taking Messages',
                        'vocabularies' => [
                            ['category' => 'Greetings', 'term' => 'Thank you for calling Youkata Stay Hotel. This is [Name]. How may I assist you?', 'details' => 'Terima kasih telah menghubungi Youkata Stay Hotel. Ini [Nama]. Bagaimana saya bisa membantu Anda?'],
                            ['category' => 'Taking Messages', 'term' => 'May I have your name and contact number, please?', 'details' => 'Boleh saya tahu nama dan nomor kontak Anda?'],
                            ['category' => 'Taking Messages', 'term' => 'I’ll pass your message to the manager.', 'details' => 'Saya akan sampaikan pesan Anda ke manajer.'],
                        ]
                    ],
                    [
                        'lesson_title' => 'Handling Inquiries and Managing Calls',
                        'vocabularies' => [
                            ['category' => 'Inquiries', 'term' => 'Let me check room availability. One moment, please.', 'details' => 'Izinkan saya cek ketersediaan kamar. Sebentar ya.'],
                            ['category' => 'Inquiries', 'term' => 'Our standard room rate is 150,000 rupiah per night.', 'details' => 'Tarif kamar standar kami 150.000 rupiah per malam.'],
                            ['category' => 'Managing Calls', 'term' => 'I’ll connect you to our housekeeping team. Please hold.', 'details' => 'Saya akan hubungkan Anda ke tim tata graha. Mohon tunggu.'],
                        ]
                    ],
                ]
            ],
            [
                'module_title' => 'Emergency Situations',
                'module_description' => 'Frasa penting untuk situasi darurat di hotel.',
                'lessons' => [
                    [
                        'lesson_title' => 'Identifying Emergencies and Giving Instructions',
                        'vocabularies' => [
                            ['category' => 'Emergency Terms', 'term' => 'fire alarm', 'details' => 'alarm kebakaran'],
                            ['category' => 'Emergency Terms', 'term' => 'evacuation route', 'details' => 'rute evakuasi'],
                            ['category' => 'Emergency Terms', 'term' => 'first aid kit', 'details' => 'kotak pertolongan pertama'],
                            ['category' => 'Instructions', 'term' => 'Please remain calm and follow me to the emergency exit.', 'details' => 'Tolong tetap tenang dan ikuti saya ke pintu darurat.'],
                            ['category' => 'Instructions', 'term' => 'Use the stairs, not the elevator.', 'details' => 'Gunakan tangga, bukan lift.'],
                        ]
                    ],
                    [
                        'lesson_title' => 'Reassuring Guests and Following Protocols',
                        'vocabularies' => [
                            ['category' => 'Reassurance', 'term' => 'You’re safe with us. We’re handling it.', 'details' => 'Anda aman bersama kami. Kami sedang menanganinya.'],
                            ['category' => 'Reassurance', 'term' => 'Help is coming soon. Please stay calm.', 'details' => 'Bantuan segera datang. Tolong tetap tenang.'],
                            ['category' => 'Reassurance', 'term' => 'We’ve called for assistance. Everything will be fine.', 'details' => 'Kami sudah panggil bantuan. Semuanya akan baik-baik saja.'],
                            ['category' => 'Reporting', 'term' => 'I’ve informed the manager about the situation.', 'details' => 'Saya sudah beri tahu manajer tentang situasinya.'],
                            ['category' => 'Reporting', 'term' => 'The fire department is on the way.', 'details' => 'Pemadam kebakaran sedang dalam perjalanan.'],
                            ['category' => 'Reporting', 'term' => 'We’ll update you as soon as possible.', 'details' => 'Kami akan beri kabar secepat mungkin.'],
                        ]
                    ],
                ]
            ],
        ];

        // 3. Loop melalui semua data dan masukkan ke database
        foreach ($courseData as $moduleData) {
            // Buat Modul
            $module = Module::create([
                'title' => $moduleData['module_title'],
                'slug' => Str::slug($moduleData['module_title']),
                'description' => $moduleData['module_description'],
                'level' => 'beginner', // Anda bisa sesuaikan ini
                'is_published' => true,
            ]);

            // Buat Lesson untuk setiap modul
            foreach ($moduleData['lessons'] as $lessonData) {
                $lesson = Lesson::create([
                    'module_id' => $module->id,
                    'title' => $lessonData['lesson_title'],
                    'slug' => Str::slug($lessonData['lesson_title']),
                ]);

                // Panggil helper untuk mengisi konten vocab
                if (!empty($lessonData['vocabularies'])) {
                    $this->seedLessonVocab($lesson, $lessonData['vocabularies']);
                }
            }
        }
    }

    /**
     * Helper function untuk mengisi data vocab per lesson.
     */
    private function seedLessonVocab(Lesson $lesson, array $vocabData)
    {
        $groupedData = collect($vocabData)->groupBy('category');

        foreach ($groupedData as $categoryName => $items) {
            // Buat kategori di tabel 'vocabularies'
            $vocabularyCategory = Vocabulary::create([
                'lesson_id' => $lesson->id,
                'category'  => $categoryName,
            ]);

            // Buat item di dalam setiap kategori
            foreach ($items as $item) {
                VocabularyItem::create([
                    'vocabulary_id' => $vocabularyCategory->id,
                    'term'          => $item['term'],
                    'details'       => $item['details'],
                ]);
            }
        }
    }
}
