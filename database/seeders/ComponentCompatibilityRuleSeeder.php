<?php

namespace Database\Seeders;

use App\Models\Component;
use App\Models\ComponentCompatibilityRule;
use Illuminate\Database\Seeder;

class ComponentCompatibilityRuleSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // LẤY COMPONENT
        // =========================

        $closedContainer = Component::where(
            'sku',
            'CP-CONTAINER-001'
        )->firstOrFail();

        $miniContainer = Component::where(
            'sku',
            'CP-CONTAINER-003'
        )->firstOrFail();

        $fittonia = Component::where(
            'sku',
            'CP-PLANT-001'
        )->firstOrFail();

        $fern = Component::where(
            'sku',
            'CP-PLANT-002'
        )->firstOrFail();

        $succulent = Component::where(
            'sku',
            'CP-PLANT-003'
        )->firstOrFail();

        $moss = Component::where(
            'sku',
            'CP-MOSS-001'
        )->firstOrFail();

        $activatedCarbon = Component::where(
            'sku',
            'CP-SUBSTRATE-002'
        )->firstOrFail();


        // =========================
        // DANH SÁCH QUY TẮC
        // =========================

        $rules = [

            // Bình kín + Fittonia
            [
                'source' => $closedContainer->id,
                'target' => $fittonia->id,
                'compatible' => true,
                'reason' =>
                    'Fittonia ưa ẩm và phù hợp với môi trường Terrarium kín.',
            ],

            // Bình kín + Dương xỉ
            [
                'source' => $closedContainer->id,
                'target' => $fern->id,
                'compatible' => true,
                'reason' =>
                    'Dương xỉ mini phù hợp với môi trường ẩm của Terrarium kín.',
            ],

            // Bình kín + Rêu
            [
                'source' => $closedContainer->id,
                'target' => $moss->id,
                'compatible' => true,
                'reason' =>
                    'Rêu xanh phát triển tốt trong môi trường có độ ẩm cao.',
            ],

            // Bình kín + Than hoạt tính
            [
                'source' => $closedContainer->id,
                'target' => $activatedCarbon->id,
                'compatible' => true,
                'reason' =>
                    'Than hoạt tính hỗ trợ kiểm soát mùi và nấm mốc trong bình kín.',
            ],

            // Bình kín + Sen đá
            [
                'source' => $closedContainer->id,
                'target' => $succulent->id,
                'compatible' => false,
                'reason' =>
                    'Sen đá không phù hợp môi trường kín có độ ẩm cao.',
            ],

            // Bình mini + Sen đá
            [
                'source' => $miniContainer->id,
                'target' => $succulent->id,
                'compatible' => true,
                'reason' =>
                    'Sen đá phù hợp với bình mini khi thiết kế hệ sinh thái khô.',
            ],

            // Sen đá + Rêu
            [
                'source' => $succulent->id,
                'target' => $moss->id,
                'compatible' => false,
                'reason' =>
                    'Sen đá cần môi trường khô trong khi rêu cần độ ẩm cao.',
            ],
        ];


        // =========================
        // LƯU DATABASE
        // =========================

        foreach ($rules as $rule) {

            ComponentCompatibilityRule::updateOrCreate(
                [
                    'source_component_id' => $rule['source'],
                    'target_component_id' => $rule['target'],
                ],
                [
                    'is_compatible' => $rule['compatible'],
                    'reason' => $rule['reason'],
                    'is_active' => true,
                ]
            );
        }
    }
}