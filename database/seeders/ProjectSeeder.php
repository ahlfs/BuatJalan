<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\TechStack;
use App\Models\Roadmap;
use App\Models\DbSchema;
use App\Models\DbColumn;
use App\Models\ProjectCost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate tables to avoid duplicates when running multiple times
        Schema::disableForeignKeyConstraints();
        Project::truncate();
        TechStack::truncate();
        Roadmap::truncate();
        DbSchema::truncate();
        DbColumn::truncate();
        ProjectCost::truncate();
        Schema::enableForeignKeyConstraints();

        $projects = [
            [
                'title' => 'SaaS E-Commerce Platform',
                'slug' => 'saas-ecommerce',
                'description' => 'Aplikasi e-commerce modern dengan checkout Stripe, catalog indexing, dan autentikasi admin.',
                'prd_markdown' => "# PRD: SaaS E-Commerce Platform\n\n## 1. Ringkasan Proyek\nMembangun platform toko online (SaaS) multitenant yang memungkinkan admin membuat toko instan dengan integrasi pembayaran Stripe.\n\n## 2. Fitur Utama\n- Katalog Produk Dinamis\n- Keranjang Belanja & Checkout Stripe\n- Dasbor Manajemen Admin & Inventaris\n- Webhook Update Status Transaksi Otomatis\n\n## 3. Spesifikasi Teknis\n- **Frontend**: React.js + Next.js + Tailwind CSS\n- **Backend**: Laravel API (Eloquent ORM)\n- **Database**: PostgreSQL\n- **Hosting**: Vercel & Railway\n\n## 4. Alur Kerja Checkout\n1. User memasukkan barang ke Cart.\n2. Klik checkout, diarahkan ke Stripe Hosted Page.\n3. Stripe mengirimkan Webhook event `checkout.session.completed`.\n4. Server Laravel mencatat transaksi sukses dan mengurangi inventaris produk.",
                'first_deployment_cost' => 515000,
                'techstack' => [
                    ['layer' => 'Frontend', 'name' => 'React.js + Next.js', 'description' => 'UI responsif, routing modern, dan Tailwind CSS styling.', 'icon' => '🗄'],
                    ['layer' => 'Backend', 'name' => 'Laravel', 'description' => 'REST APIs, Eloquent ORM, admin panel, dan order management.', 'icon' => '☁'],
                    ['layer' => 'Database', 'name' => 'PostgreSQL', 'description' => 'Penyimpanan transaksional yang andal dan aman.', 'icon' => '📁'],
                    ['layer' => 'Deployment', 'name' => 'Vercel + Railway', 'description' => 'Hosting server tanpa pusing dengan autoscaling.', 'icon' => '🚀']
                ],
                'roadmap' => [
                    ['title' => 'Perancangan Awal & PRD', 'description' => 'Mendefinisikan skema database transaksi, target user, dan memetakan user flow checkout Stripe.', 'icon' => '📋', 'time' => 'Hari 1-3'],
                    ['title' => 'Konfigurasi Database & Backend', 'description' => 'Setup Laravel API routes, schema database PostgreSQL, migration tables, dan seed produk awal.', 'icon' => '⚙️', 'time' => 'Hari 4-7'],
                    ['title' => 'Integrasi API & Frontend UI', 'description' => 'Membuat halaman katalog, cart, checkout UI menggunakan Next.js + Tailwind, dan menghubungkannya dengan API Laravel.', 'icon' => '💻', 'time' => 'Hari 8-15'],
                    ['title' => 'Sistem Pembayaran & Webhook', 'description' => 'Konfigurasi Stripe API key, mengaktifkan webhook pembayaran, dan update status transaksi otomatis.', 'icon' => '💳', 'time' => 'Hari 16-20'],
                    ['title' => 'Deployment & Uji Coba', 'description' => 'Deploy frontend di Vercel, deploy database & backend di Railway, verifikasi transaksi SSL live.', 'icon' => '🚀', 'time' => 'Hari 21-25']
                ],
                'database_schema' => [
                    [
                        'table_name' => 'users',
                        'table_desc' => 'Menyimpan data otentikasi akun admin toko dan pelanggan.',
                        'columns' => [
                            ['name' => 'id', 'type' => 'BigInt (PK)', 'nullable' => 'No', 'desc' => 'Unique identifier untuk setiap user.'],
                            ['name' => 'name', 'type' => 'VarChar(255)', 'nullable' => 'No', 'desc' => 'Nama lengkap user.'],
                            ['name' => 'email', 'type' => 'VarChar(255)', 'nullable' => 'No (Unique)', 'desc' => 'Alamat email untuk masuk login.'],
                            ['name' => 'password', 'type' => 'VarChar(255)', 'nullable' => 'No', 'desc' => 'Password hash terenkripsi.'],
                            ['name' => 'created_at', 'type' => 'Timestamp', 'nullable' => 'Yes', 'desc' => 'Waktu pembuatan akun.']
                        ]
                    ],
                    [
                        'table_name' => 'products',
                        'table_desc' => 'Menyimpan katalog produk e-commerce.',
                        'columns' => [
                            ['name' => 'id', 'type' => 'BigInt (PK)', 'nullable' => 'No', 'desc' => 'Unique identifier untuk setiap produk.'],
                            ['name' => 'name', 'type' => 'VarChar(255)', 'nullable' => 'No', 'desc' => 'Nama produk katalog.'],
                            ['name' => 'price', 'type' => 'Decimal(10,2)', 'nullable' => 'No', 'desc' => 'Harga produk dalam Rupiah.'],
                            ['name' => 'stock', 'type' => 'Integer', 'nullable' => 'No', 'desc' => 'Jumlah persediaan barang di gudang.'],
                            ['name' => 'description', 'type' => 'Text', 'nullable' => 'Yes', 'desc' => 'Deskripsi detail spesifikasi produk.']
                        ]
                    ],
                    [
                        'table_name' => 'orders',
                        'table_desc' => 'Mencatat riwayat transaksi pembelian produk.',
                        'columns' => [
                            ['name' => 'id', 'type' => 'BigInt (PK)', 'nullable' => 'No', 'desc' => 'Unique identifier transaksi.'],
                            ['name' => 'user_id', 'type' => 'BigInt (FK)', 'nullable' => 'No', 'desc' => 'Relasi ke tabel users.'],
                            ['name' => 'stripe_session_id', 'type' => 'VarChar(255)', 'nullable' => 'Yes', 'desc' => 'ID sesi pembayaran Stripe Checkout.'],
                            ['name' => 'total_amount', 'type' => 'Decimal(12,2)', 'nullable' => 'No', 'desc' => 'Total biaya yang dibayarkan.'],
                            ['name' => 'status', 'type' => 'VarChar(50)', 'nullable' => 'No', 'desc' => 'Status pembayaran (pending, paid, failed).']
                        ]
                    ]
                ],
                'costs' => [
                    'one_time' => [
                        ['name' => 'Domain .com (1 Tahun)', 'price' => 150000],
                        ['name' => 'Template UI Premium License', 'price' => 290000],
                    ],
                    'recurring' => [
                        ['name' => 'Railway Server (Backend & DB)', 'price' => 75000],
                        ['name' => 'Vercel Hobby / Pro', 'price' => 0],
                        ['name' => 'Stripe Fee (Estimasi bulanan)', 'price' => 0],
                    ]
                ]
            ],
            [
                'title' => 'Portofolio Dev Landing',
                'slug' => 'portofolio-dev',
                'description' => 'Website portofolio statis cepat dengan Astro, Tailwind CSS, dan integrasi Markdown.',
                'prd_markdown' => "# PRD: Portofolio Dev Landing\n\n## 1. Ringkasan Proyek\nLanding page portofolio profesional untuk developer, berfokus pada kecepatan muat dan optimasi SEO.\n\n## 2. Fitur Utama\n- Desain Grid Proyek Responsif\n- Integrasi Posting dengan File Markdown\n- Form Kontak Pengunjung\n- Pemuatan Aset Gambar Teroptimasi\n\n## 3. Spesifikasi Teknis\n- **Frontend**: Astro + Tailwind CSS\n- **Content**: Markdown/MDX\n- **Hosting**: Vercel CDN Global",
                'first_deployment_cost' => 180000,
                'techstack' => [
                    ['layer' => 'Frontend', 'name' => 'Astro + Tailwind CSS', 'description' => 'Pengiriman halaman super cepat dengan zero-JS default.', 'icon' => '🗄'],
                    ['layer' => 'Content Management', 'name' => 'Markdown (MDX)', 'description' => 'Penyusunan proyek dan keahlian dengan file teks.', 'icon' => '✍️'],
                    ['layer' => 'Deployment', 'name' => 'Vercel', 'description' => 'Hosting CDN static global gratis dan otomatis.', 'icon' => '🚀']
                ],
                'roadmap' => [
                    ['title' => 'Desain Layout & Kategori', 'description' => 'Membuat portofolio grid layout, section hero, dan form kontak.', 'icon' => '🎨', 'time' => 'Hari 1-2'],
                    ['title' => 'Pengisian Konten Markdown', 'description' => 'Membuat file Markdown untuk detail deskripsi projek dan sertifikasi.', 'icon' => '✍️', 'time' => 'Hari 3-4'],
                    ['title' => 'Deployment Vercel', 'description' => 'Deploy repo GitHub ke Vercel CDN dengan SSL gratis.', 'icon' => '🚀', 'time' => 'Hari 5']
                ],
                'database_schema' => [
                    [
                        'table_name' => 'projects',
                        'table_desc' => 'Menyimpan daftar proyek portofolio yang dipamerkan.',
                        'columns' => [
                            ['name' => 'id', 'type' => 'BigInt (PK)', 'nullable' => 'No', 'desc' => 'Unique identifier proyek.'],
                            ['name' => 'title', 'type' => 'VarChar(255)', 'nullable' => 'No', 'desc' => 'Judul proyek developer.'],
                            ['name' => 'description', 'type' => 'Text', 'nullable' => 'No', 'desc' => 'Rangkuman detail pengerjaan.'],
                            ['name' => 'tags', 'type' => 'VarChar(255)', 'nullable' => 'Yes', 'desc' => 'Pilihan teknologi / tag dipisahkan koma.'],
                            ['name' => 'github_url', 'type' => 'VarChar(255)', 'nullable' => 'Yes', 'desc' => 'Link repositori kode sumber.'],
                            ['name' => 'is_published', 'type' => 'Boolean', 'nullable' => 'No', 'desc' => 'Status publikasi proyek.']
                        ]
                    ]
                ],
                'costs' => [
                    'one_time' => [
                        ['name' => 'Domain .dev (1 Tahun)', 'price' => 180000],
                    ],
                    'recurring' => [
                        ['name' => 'Vercel Static Hosting', 'price' => 0],
                    ]
                ]
            ],
            [
                'title' => 'Task Manager API',
                'slug' => 'task-manager-api',
                'description' => 'RESTful API untuk task management dengan autentikasi JWT token dan database MongoDB NoSQL.',
                'prd_markdown' => "# PRD: Task Manager API\n\n## 1. Ringkasan Proyek\nRESTful API backend untuk aplikasi pelacak tugas / Trello clone dengan validasi token keamanan.\n\n## 2. Fitur Utama\n- Autentikasi Pengguna menggunakan JWT\n- Operasi CRUD Tugas (Board, Lists, Cards)\n- Pembagian Tugas & Pengingat Durasi\n- Database Dokumen Fleksibel NoSQL\n\n## 3. Spesifikasi Teknis\n- **Runtime**: Node.js + Express\n- **Database**: MongoDB Cloud Atlas\n- **Hosting**: Render instances",
                'first_deployment_cost' => 0,
                'techstack' => [
                    ['layer' => 'Backend API', 'name' => 'Node.js + Express', 'description' => 'Express framework RESTful API, validasi skema input.', 'icon' => '☁'],
                    ['layer' => 'Database', 'name' => 'MongoDB Cloud Atlas', 'description' => 'Database dokumen dinamis NoSQL untuk relasi board/cards.', 'icon' => '📁'],
                    ['layer' => 'Deployment', 'name' => 'Render runtime', 'description' => 'Server hosting gratis untuk aplikasi Node.js.', 'icon' => '🚀']
                ],
                'roadmap' => [
                    ['title' => 'Desain Schema API', 'description' => 'Membuat diagram collection untuk boards, cards, dan members.', 'icon' => '📊', 'time' => 'Hari 1-2'],
                    ['title' => 'Implementasi JWT Auth', 'description' => 'Membuat token generator dan middleware login session.', 'icon' => '🔒', 'time' => 'Hari 3-5'],
                    ['title' => 'Deploy Express API', 'description' => 'Hosting API di Render dengan database Atlas MongoDB.', 'icon' => '🚀', 'time' => 'Hari 6-7']
                ],
                'database_schema' => [
                    [
                        'table_name' => 'users (MongoDB Collection)',
                        'table_desc' => 'Menyimpan data otentikasi user token JWT.',
                        'columns' => [
                            ['name' => '_id', 'type' => 'String (PK)', 'nullable' => 'No', 'desc' => 'Unique identifier MongoDB document.'],
                            ['name' => 'username', 'type' => 'String', 'nullable' => 'No', 'desc' => 'Username akun pengguna.'],
                            ['name' => 'email', 'type' => 'String', 'nullable' => 'No (Unique)', 'desc' => 'Email login session.'],
                            ['name' => 'password_hash', 'type' => 'String', 'nullable' => 'No', 'desc' => 'Hash password keamanan.']
                        ]
                    ],
                    [
                        'table_name' => 'boards (MongoDB Collection)',
                        'table_desc' => 'Menyimpan papan proyek workspace.',
                        'columns' => [
                            ['name' => '_id', 'type' => 'String (PK)', 'nullable' => 'No', 'desc' => 'Unique identifier board.'],
                            ['name' => 'user_id', 'type' => 'String (FK)', 'nullable' => 'No', 'desc' => 'Relasi ke dokumen users.'],
                            ['name' => 'title', 'type' => 'String', 'nullable' => 'No', 'desc' => 'Nama papan tugas.'],
                            ['name' => 'color', 'type' => 'String', 'nullable' => 'Yes', 'desc' => 'Kode warna tema board.']
                        ]
                    ]
                ],
                'costs' => [
                    'one_time' => [],
                    'recurring' => [
                        ['name' => 'Render Instance Hosting', 'price' => 0],
                        ['name' => 'MongoDB Atlas Cloud storage', 'price' => 0],
                    ]
                ]
            ]
        ];

        foreach ($projects as $projData) {
            // Create project
            $project = Project::create([
                'title' => $projData['title'],
                'slug' => $projData['slug'],
                'description' => $projData['description'],
                'prd_markdown' => $projData['prd_markdown'],
                'first_deployment_cost' => $projData['first_deployment_cost'],
            ]);

            // Create tech stack
            foreach ($projData['techstack'] as $ts) {
                TechStack::create([
                    'project_id' => $project->id,
                    'layer' => $ts['layer'],
                    'name' => $ts['name'],
                    'icon' => $ts['icon'],
                    'description' => $ts['description'],
                ]);
            }

            // Create roadmap
            foreach ($projData['roadmap'] as $rm) {
                Roadmap::create([
                    'project_id' => $project->id,
                    'title' => $rm['title'],
                    'time' => $rm['time'],
                    'icon' => $rm['icon'],
                    'description' => $rm['description'],
                ]);
            }

            // Create db schema & columns
            foreach ($projData['database_schema'] as $schema) {
                $dbSchema = DbSchema::create([
                    'project_id' => $project->id,
                    'table_name' => $schema['table_name'],
                    'table_desc' => $schema['table_desc'],
                ]);

                foreach ($schema['columns'] as $col) {
                    DbColumn::create([
                        'db_schema_id' => $dbSchema->id,
                        'name' => $col['name'],
                        'type' => $col['type'],
                        'nullable' => $col['nullable'],
                        'desc' => $col['desc'],
                    ]);
                }
            }

            // Create costs (one_time)
            foreach ($projData['costs']['one_time'] as $cost) {
                ProjectCost::create([
                    'project_id' => $project->id,
                    'type' => 'one_time',
                    'name' => $cost['name'],
                    'price' => $cost['price'],
                ]);
            }

            // Create costs (recurring)
            foreach ($projData['costs']['recurring'] as $cost) {
                ProjectCost::create([
                    'project_id' => $project->id,
                    'type' => 'recurring',
                    'name' => $cost['name'],
                    'price' => $cost['price'],
                ]);
            }
        }
    }
}
