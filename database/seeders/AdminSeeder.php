<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Administrator;
use App\Models\User;
use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createSuperAdmin();

        echo "✅ 1 administrateur créé\n";
    }

    private function createSuperAdmin(): void
    {
        // Vérifier s'il existe déjà un admin
        $existingAdmin = User::where('user_type', 'administrator')
            ->where('email', 'admin@cinephoria.fr')
            ->first();

        if ($existingAdmin) {
            echo "⚠️  Administrateur admin@cinephoria.fr existe déjà\n";

            return;
        }

        // Création User
        $user = User::create([
            'uuid'              => Str::uuid()->toString(),
            'email'             => 'admin@cinephoria.fr',
            'user_type'         => 'administrator',
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);

        if (! $user) {
            echo "⚠️  Erreur création utilisateur administrateur\n";

            return;
        }

        // Création Administrator
        $admin = Administrator::create([
            'uuid'       => Str::uuid()->toString(),
            'user_id'    => $user->id,
            'first_name' => 'Super',
            'last_name'  => 'Admin',
            'is_active'  => true,
        ]);

        // Mettre à jour le profile_id dans la table users pour la relation polymorphe
        $user->profile_id = $admin->id;
        $user->save();

        // Création UserPassword avec mot de passe spécifique
        DB::table('user_passwords')->insert([
            'user_id'       => $user->id,
            'password_hash' => Hash::make('admazerty1234!'),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        echo "✅ Super administrateur créé - admin@cinephoria.fr / admazerty1234!\n";
    }
}
