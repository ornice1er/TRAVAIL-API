<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seeder pour la Direction Générale du Budget (DGB) et la mise à jour du Ministre.
 *
 * Source : https://budgetbenin.bj/missions-et-attributions-2/ et https://budgetbenin.bj/la-dgb/
 *
 * Idempotent : peut être relancé sans créer de doublons.
 *
 * Note : on utilise le query builder DB plutôt que les modèles Eloquent car le modèle
 * Structure déclare le trait HasUuids alors que la table `structures` possède un `id`
 * entier auto-incrémenté (insertion via le modèle => "Incorrect integer value").
 */
class BudgetSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedMinistre();
        $this->seedDirectionGeneraleBudget();
    }

    /**
     * Le ministre actuellement en charge est le Directeur Général du Budget (DGB).
     * On met à jour la structure de type "parent" (le Ministère).
     */
    private function seedMinistre(): void
    {
        $type = DB::table('types_structures')->where('is_parent', true)->first();

        if (! $type) {
            return;
        }

        $structure = DB::table('structures')
            ->where('type_structure_id', $type->id)
            ->orderBy('id')
            ->first();

        if (! $structure) {
            return;
        }

        DB::table('structures')->where('id', $structure->id)->update([
            'name'                   => 'Ministère du Budget et de la Fonction Publique',
            'acronym'                => 'MBFP',
            'name_responsable'       => 'Rodrigue CHAOU',
            'fonction'               => 'Directeur Général du Budget',
            'photo'                  => 'dgb-rodrigue-chaou.png',
            'photo_responsable'      => 'dgb-rodrigue-chaou.png',
            'biographie_responsable' => $this->biographieDgb(),
            'updated_at'             => now(),
        ]);
    }

    /**
     * Crée / met à jour la Direction Générale du Budget et ses données associées.
     */
    private function seedDirectionGeneraleBudget(): void
    {
        $typeDg = DB::table('types_structures')->where('title', 'Directions générales')->first();

        $values = [
            'name'                   => 'Direction Générale du Budget',
            'slug'                   => Str::slug('Direction Générale du Budget'),
            'name_responsable'       => 'Rodrigue CHAOU',
            'fonction'               => 'Directeur Général du Budget',
            'photo'                  => 'dgb-rodrigue-chaou.png',
            'photo_responsable'      => 'dgb-rodrigue-chaou.png',
            'biographie_responsable' => $this->biographieDgb(),
            'email'                  => 'contact@budgetbenin.bj',
            'phone'                  => '+229 21 30 09 07',
            'vision'                 => $this->mission(),
            'responsable_text'       => $this->attributions(),
            'type_structure_id'      => $typeDg?->id,
            'updated_at'             => now(),
        ];

        $existing = DB::table('structures')->where('acronym', 'DGB')->first();

        if ($existing) {
            DB::table('structures')->where('id', $existing->id)->update($values);
            $structureId = $existing->id;
        } else {
            $structureId = DB::table('structures')->insertGetId(array_merge($values, [
                'acronym'    => 'DGB',
                'created_at' => now(),
            ]));
        }

        // Mission & Attributions exposées via le mécanisme AOF (comme les autres directions)
        $media = DB::table('media')
            ->where('structure_id', $structureId)
            ->where('type', 'aof')
            ->first();

        $mediaValues = [
            'code'                 => 'AOF-DGB',
            'is_published'         => true,
            'is_archived'          => false,
            'has_principal_access' => true,
            'updated_at'           => now(),
        ];

        if ($media) {
            DB::table('media')->where('id', $media->id)->update($mediaValues);
            $mediaId = $media->id;
        } else {
            $mediaId = DB::table('media')->insertGetId(array_merge($mediaValues, [
                'structure_id' => $structureId,
                'type'         => 'aof',
                'created_at'   => now(),
            ]));
        }

        $aofValues = [
            'mission'     => $this->mission(),
            'attribution' => $this->attributions(),
            'updated_at'  => now(),
        ];

        $aof = DB::table('a_o_f_s')->where('media_id', $mediaId)->first();

        if ($aof) {
            DB::table('a_o_f_s')->where('id', $aof->id)->update($aofValues);
        } else {
            DB::table('a_o_f_s')->insert(array_merge($aofValues, [
                'media_id'   => $mediaId,
                'created_at' => now(),
            ]));
        }

        // Directions opérationnelles (grille "Directions & Services")
        DB::table('teams')->where('structure_id', $structureId)->where('type', 'autre')->delete();

        foreach ($this->directionsOperationnelles() as $direction) {
            DB::table('teams')->insert([
                'type'         => 'autre',
                'name'         => $direction['name'],
                'office'       => $direction['office'],
                'photo'        => '',
                'structure_id' => $structureId,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }

    private function biographieDgb(): string
    {
        return <<<HTML
<p>Monsieur <strong>Rodrigue CHAOU</strong> est le Directeur Général du Budget (DGB).</p>
<p>Sous son impulsion, la Direction Générale du Budget poursuit la modernisation de la gestion
des finances publiques, avec la conviction que ses activités « doivent être mises à la disposition
du public, bénéficiaire privilégié de ses prestations ».</p>
HTML;
    }

    private function mission(): string
    {
        return <<<HTML
<p>La Direction Générale du Budget (DGB) assure la définition et la mise en œuvre de la stratégie
des finances publiques ainsi que le pilotage du cadre réglementaire de gestion des investissements publics.</p>
HTML;
    }

    private function attributions(): string
    {
        return <<<HTML
<p>À ce titre, la Direction Générale du Budget est chargée de :</p>
<ul>
  <li>Élaborer les projets de loi de finances initiale et rectificative, suivre et piloter leur exécution ;</li>
  <li>Assurer la régulation budgétaire et la soutenabilité des finances publiques ;</li>
  <li>Traiter les aspects technique, juridique et financier du budget de l'État ;</li>
  <li>Concevoir, mettre en œuvre, suivre et évaluer les réformes budgétaires ;</li>
  <li>Piloter l'écosystème « transparence budgétaire, participation publique et redevabilité » ;</li>
  <li>Appliquer le code des pensions civiles et militaires de retraite ;</li>
  <li>Former et renforcer les compétences du personnel administratif ;</li>
  <li>Administrer les systèmes d'information financière ;</li>
  <li>Piloter la répartition des dépenses accidentelles et imprévisibles ;</li>
  <li>Assurer l'exercice de la fonction solde de l'État ;</li>
  <li>Examiner les réglementations en matière de rémunérations ;</li>
  <li>Définir le cadre réglementaire de gestion des investissements publics ;</li>
  <li>Assurer la programmation financière des investissements en adéquation avec les stratégies de développement.</li>
</ul>
<p><strong>Structures rattachées au Directeur Général :</strong></p>
<ul>
  <li>Assistant du Directeur Général ;</li>
  <li>Secrétariat du Directeur Général ;</li>
  <li>Pôle Budget Ouvert (PBO) ;</li>
  <li>Cellule de la Réforme Budgétaire et de la Modernisation de la Gestion Publique (CRBMGP) ;</li>
  <li>Cellule de Contrôle Interne (CCI) ;</li>
  <li>Coordination des Régions de la Direction Générale du Budget (CRDGB).</li>
</ul>
HTML;
    }

    /**
     * @return array<int, array{name: string, office: string}>
     */
    private function directionsOperationnelles(): array
    {
        return [
            ['name' => 'Direction de la Programmation, des Politiques et Synthèses Budgétaires', 'office' => 'DPPSB'],
            ['name' => 'Direction du Suivi des Investissements en Portefeuille et des Provisions', 'office' => 'DSTPP'],
            ['name' => "Direction de l'Analyse et de la Synthèse des Performances", 'office' => 'DASP'],
            ['name' => 'Direction des Pensions et des Rentes Viagères', 'office' => 'DPRV'],
            ['name' => 'Direction de la Solde', 'office' => 'DS'],
            ['name' => 'Direction de la Gestion des Ressources', 'office' => 'DGR'],
            ['name' => "Direction de l'Informatique", 'office' => 'DI'],
            ['name' => "Direction des Relations avec les Collectivités Territoriales et les Opérateurs de l'État", 'office' => 'DRCTOE'],
            ['name' => "Centre de Formation Professionnelle de l'Administration Centrale des Finances", 'office' => 'CFP-ACF'],
        ];
    }
}
