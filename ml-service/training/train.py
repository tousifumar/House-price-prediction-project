"""
Multi-Model Training & Evaluation Pipeline
Matches Section 20 of specification:
Trains & compares:
1. Linear Regression
2. Decision Tree Regressor
3. Random Forest Regressor
4. Gradient Boosting Regressor
5. XGBoost Regressor
Evaluates MAE, RMSE, R², MAPE and persists the best model and pipeline with Joblib.
"""
import json
import os
import sys
import joblib
import numpy as np
import pandas as pd
from datetime import datetime

# Ensure ml-service root is in sys.path
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
SERVICE_ROOT = os.path.abspath(os.path.join(SCRIPT_DIR, '..'))
if SERVICE_ROOT not in sys.path:
    sys.path.insert(0, SERVICE_ROOT)

from app.preprocessing import PreprocessingPipeline
from sklearn.model_selection import train_test_split
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
from sklearn.linear_model import LinearRegression
from sklearn.tree import DecisionTreeRegressor
from sklearn.ensemble import RandomForestRegressor, GradientBoostingRegressor
from xgboost import XGBRegressor

def calculate_mape(y_true, y_pred) -> float:
    return float(np.mean(np.abs((y_true - y_pred) / y_true)))

def run_training():
    dataset_path = os.path.join(SERVICE_ROOT, 'datasets', 'bangalore_house_prices.csv')
    if not os.path.exists(dataset_path):
        print("Dataset not found. Generating dataset first...")
        from training.generate_dataset import generate_dataset
        df = generate_dataset(3000)
        df.to_csv(dataset_path, index=False)
    else:
        df = pd.read_csv(dataset_path)

    print(f"Loaded dataset: {len(df)} rows, {len(df.columns)} columns.")

    # Separate features and target
    X = df.drop(columns=['id', 'price'])
    y = df['price'].values

    # Train / Test split (80% train, 20% test)
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.20, random_state=42
    )

    print(f"Training split: {len(X_train)} samples, Test split: {len(X_test)} samples.")

    # Fit preprocessing pipeline on training data
    pipeline = PreprocessingPipeline()
    X_train_proc = pipeline.fit_transform(X_train)
    X_test_proc = pipeline.transform(X_test)

    # Models to evaluate
    models = {
        'Linear Regression': LinearRegression(),
        'Decision Tree Regressor': DecisionTreeRegressor(max_depth=12, random_state=42),
        'Random Forest Regressor': RandomForestRegressor(n_estimators=200, max_depth=14, random_state=42, n_jobs=-1),
        'Gradient Boosting Regressor': GradientBoostingRegressor(n_estimators=200, learning_rate=0.08, max_depth=5, random_state=42),
        'XGBoost Regressor': XGBRegressor(n_estimators=250, learning_rate=0.06, max_depth=6, random_state=42, n_jobs=-1),
    }

    results = {}
    best_name = None
    best_r2 = -float('inf')
    trained_models = {}

    print("\nEvaluating Regression Models:")
    print("=" * 80)
    print(f"{'Model':<30} | {'R2 Score':<10} | {'MAE (INR)':<15} | {'RMSE (INR)':<15} | {'MAPE':<8}")
    print("-" * 80)

    for name, model in models.items():
        model.fit(X_train_proc, y_train)
        preds = model.predict(X_test_proc)

        r2 = float(r2_score(y_test, preds))
        mae = float(mean_absolute_error(y_test, preds))
        rmse = float(np.sqrt(mean_squared_error(y_test, preds)))
        mape = float(calculate_mape(y_test, preds))

        results[name] = {
            'r2_score': round(r2, 4),
            'mae': round(mae, 2),
            'rmse': round(rmse, 2),
            'mape': round(mape, 4),
        }
        trained_models[name] = model

        print(f"{name:<30} | {r2:<10.4f} | INR {mae:<10,.0f} | INR {rmse:<10,.0f} | {mape:<8.2%}")

        if r2 > best_r2:
            best_r2 = r2
            best_name = name

    print("=" * 80)
    print(f"Identified Best Model: {best_name} (R²: {best_r2:.4f})")

    best_model = trained_models[best_name]

    # Save artifacts
    models_dir = os.path.join(SERVICE_ROOT, 'models')
    os.makedirs(models_dir, exist_ok=True)

    # 1. Save pipeline
    preprocessor_path = os.path.join(models_dir, 'preprocessor_v1.0.joblib')
    pipeline.save(preprocessor_path)

    # 2. Save best model
    model_path = os.path.join(models_dir, 'active_model.joblib')
    joblib.dump(best_model, model_path)

    # 3. Calculate feature importances
    feature_names = pipeline.feature_names_out
    importances = {}
    if hasattr(best_model, 'feature_importances_'):
        raw_imp = best_model.feature_importances_
        # Aggregate top features
        for f_name, imp in sorted(zip(feature_names, raw_imp), key=lambda x: x[1], reverse=True)[:15]:
            importances[f_name] = round(float(imp), 4)

    # 4. Save metadata
    version = "v1.0-xgb" if "XGBoost" in best_name else "v1.0-rf"
    metadata = {
        'version': version,
        'algorithm': best_name,
        'best_r2_score': results[best_name]['r2_score'],
        'best_mae': results[best_name]['mae'],
        'best_rmse': results[best_name]['rmse'],
        'best_mape': results[best_name]['mape'],
        'comparison_metrics': results,
        'top_feature_importance': importances,
        'dataset': 'bangalore_house_prices.csv',
        'training_samples': len(X_train),
        'test_samples': len(X_test),
        'trained_at': datetime.now().isoformat(),
        'status': 'active',
    }

    metadata_path = os.path.join(models_dir, 'model_metadata.json')
    with open(metadata_path, 'w') as f:
        json.dump(metadata, f, indent=2)

    print(f"\nArtifacts successfully persisted:")
    print(f"  - Model:        {model_path}")
    print(f"  - Preprocessor: {preprocessor_path}")
    print(f"  - Metadata:     {metadata_path}")

    return metadata

if __name__ == '__main__':
    run_training()
