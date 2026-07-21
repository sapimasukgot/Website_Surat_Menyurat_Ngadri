<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * 50 akun operator untuk dibagikan ke perangkat/staf kantor desa.
 * Daftar email & password awal: berkas akun/50-akun-operator.xlsx.
 *
 * Aman dijalankan berulang: akun yang SUDAH ADA tidak disentuh sama sekali,
 * sehingga password yang telah diganti pemiliknya tidak akan ter-reset.
 */
class OperatorUserSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->accounts() as $account) {
            User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'jabatan' => 'Operator',
                    'password' => Hash::make($account['password']),
                    'email_verified_at' => now(),
                ]
            );
        }
    }

    private function accounts(): array
    {
        return [
            ['email' => 'operator01@desa.test', 'name' => 'Operator 01', 'password' => 'Z9KJgc9nQ8'],
            ['email' => 'operator02@desa.test', 'name' => 'Operator 02', 'password' => '6ggFc3KYTn'],
            ['email' => 'operator03@desa.test', 'name' => 'Operator 03', 'password' => 'hxNNksGrFn'],
            ['email' => 'operator04@desa.test', 'name' => 'Operator 04', 'password' => 'tR4wp3KkdK'],
            ['email' => 'operator05@desa.test', 'name' => 'Operator 05', 'password' => 'eHsDPpSqAu'],
            ['email' => 'operator06@desa.test', 'name' => 'Operator 06', 'password' => 'e7kcp2cs7z'],
            ['email' => 'operator07@desa.test', 'name' => 'Operator 07', 'password' => 'K4Jd5qMw2N'],
            ['email' => 'operator08@desa.test', 'name' => 'Operator 08', 'password' => '9kRsT8aJRS'],
            ['email' => 'operator09@desa.test', 'name' => 'Operator 09', 'password' => '9WEVC8Pw9U'],
            ['email' => 'operator10@desa.test', 'name' => 'Operator 10', 'password' => 'nqDqk9ckAr'],
            ['email' => 'operator11@desa.test', 'name' => 'Operator 11', 'password' => 'X8CZ3M4nVx'],
            ['email' => 'operator12@desa.test', 'name' => 'Operator 12', 'password' => 'VWqEwEbn3d'],
            ['email' => 'operator13@desa.test', 'name' => 'Operator 13', 'password' => 'FTyERnDA7V'],
            ['email' => 'operator14@desa.test', 'name' => 'Operator 14', 'password' => 'ryg9RDfqtd'],
            ['email' => 'operator15@desa.test', 'name' => 'Operator 15', 'password' => 'GTEEjxtkqg'],
            ['email' => 'operator16@desa.test', 'name' => 'Operator 16', 'password' => 'A5CQ3eTNKs'],
            ['email' => 'operator17@desa.test', 'name' => 'Operator 17', 'password' => 'a5CmDznh4A'],
            ['email' => 'operator18@desa.test', 'name' => 'Operator 18', 'password' => 'gjXn4YBPRu'],
            ['email' => 'operator19@desa.test', 'name' => 'Operator 19', 'password' => 'Ur4p6gx76Z'],
            ['email' => 'operator20@desa.test', 'name' => 'Operator 20', 'password' => 'JJZbsj5RKs'],
            ['email' => 'operator21@desa.test', 'name' => 'Operator 21', 'password' => 'Acx9nAq8XT'],
            ['email' => 'operator22@desa.test', 'name' => 'Operator 22', 'password' => 'zwTHGEpWAT'],
            ['email' => 'operator23@desa.test', 'name' => 'Operator 23', 'password' => 'W4PQhJHDWd'],
            ['email' => 'operator24@desa.test', 'name' => 'Operator 24', 'password' => '74s5jPhDAz'],
            ['email' => 'operator25@desa.test', 'name' => 'Operator 25', 'password' => '88FhU8QUTk'],
            ['email' => 'operator26@desa.test', 'name' => 'Operator 26', 'password' => 'NmdByhbbJz'],
            ['email' => 'operator27@desa.test', 'name' => 'Operator 27', 'password' => 'gFAVNfYEjj'],
            ['email' => 'operator28@desa.test', 'name' => 'Operator 28', 'password' => '33hSZ3nmyF'],
            ['email' => 'operator29@desa.test', 'name' => 'Operator 29', 'password' => 'zZStz9Qjws'],
            ['email' => 'operator30@desa.test', 'name' => 'Operator 30', 'password' => '4EMf87e7bj'],
            ['email' => 'operator31@desa.test', 'name' => 'Operator 31', 'password' => 'Dgw6wKWMkr'],
            ['email' => 'operator32@desa.test', 'name' => 'Operator 32', 'password' => 'CqdE7JZN6A'],
            ['email' => 'operator33@desa.test', 'name' => 'Operator 33', 'password' => 'T8sJWWF7kN'],
            ['email' => 'operator34@desa.test', 'name' => 'Operator 34', 'password' => 'xB4cvnDAEj'],
            ['email' => 'operator35@desa.test', 'name' => 'Operator 35', 'password' => 'txSNrjsEBm'],
            ['email' => 'operator36@desa.test', 'name' => 'Operator 36', 'password' => 'kA4fxMRZzM'],
            ['email' => 'operator37@desa.test', 'name' => 'Operator 37', 'password' => 'FBvgA4FNRj'],
            ['email' => 'operator38@desa.test', 'name' => 'Operator 38', 'password' => 'fvD4ZcGvY6'],
            ['email' => 'operator39@desa.test', 'name' => 'Operator 39', 'password' => 'psA4HyBJc8'],
            ['email' => 'operator40@desa.test', 'name' => 'Operator 40', 'password' => '98mdA7mvAP'],
            ['email' => 'operator41@desa.test', 'name' => 'Operator 41', 'password' => 'Y8XTv3wQAq'],
            ['email' => 'operator42@desa.test', 'name' => 'Operator 42', 'password' => 'VAahvFCbTE'],
            ['email' => 'operator43@desa.test', 'name' => 'Operator 43', 'password' => 'YQhaPGAnhx'],
            ['email' => 'operator44@desa.test', 'name' => 'Operator 44', 'password' => 'ThqbBVRBK7'],
            ['email' => 'operator45@desa.test', 'name' => 'Operator 45', 'password' => 'GaJnd4M4ku'],
            ['email' => 'operator46@desa.test', 'name' => 'Operator 46', 'password' => 'Sw8eVbz23x'],
            ['email' => 'operator47@desa.test', 'name' => 'Operator 47', 'password' => 'Gaf9b7t4yx'],
            ['email' => 'operator48@desa.test', 'name' => 'Operator 48', 'password' => '4b4YCXwd45'],
            ['email' => 'operator49@desa.test', 'name' => 'Operator 49', 'password' => 'Mbjh5c9fu3'],
            ['email' => 'operator50@desa.test', 'name' => 'Operator 50', 'password' => 'X95tAJjmsq'],
        ];
    }
}
