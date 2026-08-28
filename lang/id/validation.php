<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pesan Validasi Bahasa Indonesia — Sekolahin (SPMB)
    |--------------------------------------------------------------------------
    |
    | Berkas ini menyediakan pesan validasi default dalam Bahasa Indonesia
    | yang ramah pengguna. Placeholder :attribute akan diganti dengan label
    | field yang lebih mudah dipahami (lihat 'attributes' di bawah).
    |
    */

    'accepted'             => ':attribute harus disetujui.',
    'accepted_if'          => ':attribute harus disetujui ketika :other bernilai :value.',
    'active_url'           => ':attribute bukan URL yang valid.',
    'after'                => ':attribute harus tanggal setelah :date.',
    'after_or_equal'       => ':attribute harus tanggal setelah atau sama dengan :date.',
    'alpha'                => ':attribute hanya boleh berisi huruf.',
    'alpha_dash'           => ':attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num'            => ':attribute hanya boleh berisi huruf dan angka.',
    'array'                => ':attribute harus berupa daftar.',
    'ascii'                => ':attribute hanya boleh berisi karakter alfanumerik dan simbol satu byte.',
    'before'               => ':attribute harus tanggal sebelum :date.',
    'before_or_equal'      => ':attribute harus tanggal sebelum atau sama dengan :date.',
    'between'              => [
        'array'   => ':attribute harus berisi antara :min sampai :max item.',
        'file'    => ':attribute harus berukuran antara :min sampai :max kilobyte.',
        'numeric' => ':attribute harus bernilai antara :min sampai :max.',
        'string'  => ':attribute harus berisi antara :min sampai :max karakter.',
    ],
    'boolean'              => ':attribute harus bernilai benar atau salah.',
    'can'                  => ':attribute berisi nilai yang tidak diizinkan.',
    'confirmed'            => 'Konfirmasi :attribute tidak cocok.',
    'contains'             => ':attribute kehilangan nilai yang wajib diisi.',
    'current_password'     => 'Kata sandi yang Anda masukkan salah.',
    'date'                 => ':attribute bukan tanggal yang valid.',
    'date_equals'          => ':attribute harus tanggal yang sama dengan :date.',
    'date_format'          => ':attribute harus sesuai format :format.',
    'decimal'              => ':attribute harus memiliki :decimal angka di belakang koma.',
    'declined'             => ':attribute harus ditolak.',
    'declined_if'          => ':attribute harus ditolak ketika :other bernilai :value.',
    'different'            => ':attribute dan :other harus berbeda.',
    'digits'               => ':attribute harus terdiri dari :digits digit.',
    'digits_between'       => ':attribute harus terdiri dari :min sampai :max digit.',
    'dimensions'           => ':attribute memiliki ukuran gambar yang tidak valid.',
    'distinct'             => ':attribute memiliki nilai yang duplikat.',
    'doesnt_end_with'      => ':attribute tidak boleh diakhiri dengan: :values.',
    'doesnt_start_with'    => ':attribute tidak boleh diawali dengan: :values.',
    'email'                => ':attribute harus berupa alamat email yang valid.',
    'ends_with'            => ':attribute harus diakhiri dengan salah satu: :values.',
    'enum'                 => ':attribute yang dipilih tidak valid.',
    'exists'               => ':attribute yang dipilih tidak valid.',
    'extensions'           => ':attribute harus memiliki salah satu ekstensi berikut: :values.',
    'file'                 => ':attribute harus berupa berkas.',
    'filled'               => ':attribute wajib diisi.',
    'gt'                   => [
        'array'   => ':attribute harus lebih dari :value item.',
        'file'    => ':attribute harus lebih besar dari :value kilobyte.',
        'numeric' => ':attribute harus lebih besar dari :value.',
        'string'  => ':attribute harus lebih dari :value karakter.',
    ],
    'gte'                  => [
        'array'   => ':attribute harus berisi :value item atau lebih.',
        'file'    => ':attribute harus lebih besar dari atau sama dengan :value kilobyte.',
        'numeric' => ':attribute harus lebih besar dari atau sama dengan :value.',
        'string'  => ':attribute harus lebih dari atau sama dengan :value karakter.',
    ],
    'hex_color'            => ':attribute harus berupa warna heksadesimal yang valid.',
    'image'                => ':attribute harus berupa gambar.',
    'in'                   => ':attribute yang dipilih tidak valid.',
    'in_array'             => ':attribute harus ada di dalam :other.',
    'integer'              => ':attribute harus berupa bilangan bulat.',
    'ip'                   => ':attribute harus berupa alamat IP yang valid.',
    'ipv4'                 => ':attribute harus berupa alamat IPv4 yang valid.',
    'ipv6'                 => ':attribute harus berupa alamat IPv6 yang valid.',
    'json'                 => ':attribute harus berupa teks JSON yang valid.',
    'list'                 => ':attribute harus berupa daftar.',
    'lowercase'            => ':attribute harus berupa huruf kecil semua.',
    'lt'                   => [
        'array'   => ':attribute harus kurang dari :value item.',
        'file'    => ':attribute harus lebih kecil dari :value kilobyte.',
        'numeric' => ':attribute harus lebih kecil dari :value.',
        'string'  => ':attribute harus kurang dari :value karakter.',
    ],
    'lte'                  => [
        'array'   => ':attribute tidak boleh lebih dari :value item.',
        'file'    => ':attribute harus lebih kecil dari atau sama dengan :value kilobyte.',
        'numeric' => ':attribute harus lebih kecil dari atau sama dengan :value.',
        'string'  => ':attribute harus kurang dari atau sama dengan :value karakter.',
    ],
    'mac_address'          => ':attribute harus berupa alamat MAC yang valid.',
    'max'                  => [
        'array'   => ':attribute tidak boleh lebih dari :max item.',
        'file'    => ':attribute tidak boleh lebih besar dari :max kilobyte.',
        'numeric' => ':attribute tidak boleh lebih besar dari :max.',
        'string'  => ':attribute tidak boleh lebih dari :max karakter.',
    ],
    'max_digits'           => ':attribute tidak boleh lebih dari :max digit.',
    'mimes'                => ':attribute harus berupa berkas dengan tipe: :values.',
    'mimetypes'            => ':attribute harus berupa berkas dengan tipe: :values.',
    'min'                  => [
        'array'   => ':attribute harus berisi minimal :min item.',
        'file'    => ':attribute harus berukuran minimal :min kilobyte.',
        'numeric' => ':attribute harus bernilai minimal :min.',
        'string'  => ':attribute harus berisi minimal :min karakter.',
    ],
    'min_digits'           => ':attribute harus terdiri dari minimal :min digit.',
    'missing'              => ':attribute tidak boleh diisi.',
    'missing_if'           => ':attribute tidak boleh diisi ketika :other bernilai :value.',
    'missing_unless'       => ':attribute tidak boleh diisi kecuali :other bernilai :values.',
    'missing_with'         => ':attribute tidak boleh diisi ketika :values diisi.',
    'missing_with_all'     => ':attribute tidak boleh diisi ketika :values diisi.',
    'multiple_of'          => ':attribute harus kelipatan dari :value.',
    'not_in'               => ':attribute yang dipilih tidak valid.',
    'not_regex'            => 'Format :attribute tidak valid.',
    'numeric'              => ':attribute harus berupa angka.',
    'password'             => [
        'letters'       => ':attribute harus mengandung minimal satu huruf.',
        'mixed'         => ':attribute harus mengandung huruf besar dan huruf kecil.',
        'numbers'       => ':attribute harus mengandung minimal satu angka.',
        'symbols'       => ':attribute harus mengandung minimal satu simbol.',
        'uncompromised' => ':attribute yang Anda pilih pernah muncul di kebocoran data. Silakan pilih :attribute lain.',
    ],
    'present'              => ':attribute wajib ada.',
    'present_if'           => ':attribute wajib ada ketika :other bernilai :value.',
    'present_unless'       => ':attribute wajib ada kecuali :other bernilai :values.',
    'present_with'         => ':attribute wajib ada ketika :values diisi.',
    'present_with_all'     => ':attribute wajib ada ketika :values diisi.',
    'prohibited'           => ':attribute tidak boleh diisi.',
    'prohibited_if'        => ':attribute tidak boleh diisi ketika :other bernilai :value.',
    'prohibited_if_accepted' => ':attribute tidak boleh diisi ketika :other diterima.',
    'prohibited_if_declined' => ':attribute tidak boleh diisi ketika :other ditolak.',
    'prohibited_unless'    => ':attribute tidak boleh diisi kecuali :other bernilai :values.',
    'prohibits'            => ':attribute melarang :other untuk diisi.',
    'regex'                => 'Format :attribute tidak valid.',
    'required'             => ':attribute wajib diisi.',
    'required_array_keys'  => ':attribute harus berisi entri untuk: :values.',
    'required_if'          => ':attribute wajib diisi ketika :other bernilai :value.',
    'required_if_accepted' => ':attribute wajib diisi ketika :other diterima.',
    'required_if_declined' => ':attribute wajib diisi ketika :other ditolak.',
    'required_unless'      => ':attribute wajib diisi kecuali :other bernilai :values.',
    'required_with'        => ':attribute wajib diisi ketika :values diisi.',
    'required_with_all'    => ':attribute wajib diisi ketika :values diisi.',
    'required_without'     => ':attribute wajib diisi ketika :values tidak diisi.',
    'required_without_all' => ':attribute wajib diisi ketika tidak ada :values yang diisi.',
    'same'                 => ':attribute harus sama dengan :other.',
    'size'                 => [
        'array'   => ':attribute harus berisi :size item.',
        'file'    => ':attribute harus berukuran :size kilobyte.',
        'numeric' => ':attribute harus bernilai :size.',
        'string'  => ':attribute harus berisi :size karakter.',
    ],
    'starts_with'          => ':attribute harus diawali dengan salah satu: :values.',
    'string'               => ':attribute harus berupa teks.',
    'timezone'             => ':attribute harus berupa zona waktu yang valid.',
    'unique'               => ':attribute sudah digunakan.',
    'uploaded'             => ':attribute gagal diunggah.',
    'uppercase'            => ':attribute harus berupa huruf besar semua.',
    'url'                  => ':attribute harus berupa URL yang valid.',
    'ulid'                 => ':attribute harus berupa ULID yang valid.',
    'uuid'                 => ':attribute harus berupa UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Pesan Validasi Kustom (per-field)
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'password' => [
            'confirmed' => 'Konfirmasi kata sandi tidak cocok dengan kata sandi yang dimasukkan.',
        ],
        'password_confirmation' => [
            'required' => 'Ulangi kata sandi wajib diisi.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Label Atribut (diganti menjadi bahasa yang natural)
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name'                  => 'Nama Lengkap',
        'email'                 => 'Email',
        'password'              => 'Kata Sandi',
        'password_confirmation' => 'Konfirmasi Kata Sandi',
        'terms'                 => 'Persetujuan Syarat & Ketentuan',
        'avatar'                => 'Foto Profil',
        'current_password'      => 'Kata Sandi Saat Ini',
        'new_password'          => 'Kata Sandi Baru',
        'phone'                 => 'Nomor Telepon',
        'address'               => 'Alamat',
        'birth_date'            => 'Tanggal Lahir',
        'birth_place'           => 'Tempat Lahir',
        'gender'                => 'Jenis Kelamin',
        'nik'                   => 'NIK',
        'nisn'                  => 'NISN',
        'school_origin'         => 'Asal Sekolah',
        'graduation_year'       => 'Tahun Lulus',
        'document'              => 'Dokumen',
        'photo'                 => 'Foto',
        'file'                  => 'Berkas',
        'image'                 => 'Gambar',
    ],

];
