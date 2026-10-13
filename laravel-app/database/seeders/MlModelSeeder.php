<?php

namespace Database\Seeders;

use App\Models\MlModel;
use App\Models\ModelMetric;
use Illuminate\Database\Seeder;

class MlModelSeeder extends Seeder
{
    public function run(): void
    {
        $model = MlModel::updateOrCreate(
            ['version' => 'v1.0-xgb'],
            [
                'name' => 'Bangalore Urban Valuation Model',
                'algorithm' => 'XGBoost Regressor',
                'model_path' => 'models/xgb_house_price_v1.0.joblib',
                'preprocessor_path' => 'models/preprocessor_v1.0.joblib',
                'mae' => 485000.00, // ₹4.85 L
                'rmse' => 820000.00, // ₹8.20 L
                'r2_score' => 0.9142, // 91.4%
                'mape' => 0.0520, // 5.2%
                'hyperparameters' => [
                    'n_estimators' => 300,
                    'max_depth' => 6,
                    'learning_rate' => 0.05,
                    'subsample' => 0.85,
                    'colsample_bytree' => 0.85,
                ],
                'feature_importance' => [
                    'area' => 0.32,
                    'locality_avg_price' => 0.28,
                    'location_score' => 0.14,
                    'bedrooms' => 0.10,
                    'property_age' => 0.06,
                    'bathrooms' => 0.05,
                    'amenities_count' => 0.03,
                    'floor_ratio' => 0.02,
                ],
                'status' => 'active',
                'trained_at' => now()->subDays(3),
            ]
        );

        $metrics = [
            ['metric_name' => 'R2 Score', 'metric_value' => 0.9142, 'dataset_split' => 'test'],
            ['metric_name' => 'MAE', 'metric_value' => 485000.00, 'dataset_split' => 'test'],
            ['metric_name' => 'RMSE', 'metric_value' => 820000.00, 'dataset_split' => 'test'],
            ['metric_name' => 'MAPE', 'metric_value' => 0.0520, 'dataset_split' => 'test'],
        ];

        foreach ($metrics as $metric) {
            ModelMetric::updateOrCreate(
                [
                    'model_id' => $model->id,
                    'metric_name' => $metric['metric_name'],
                    'dataset_split' => $metric['dataset_split'],
                ],
                $metric
            );
        }
    }
}
