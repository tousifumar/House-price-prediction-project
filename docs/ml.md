# Machine Learning Architecture & Pipeline

## 1. Machine Learning Pipeline Overview
The ML service operates independently from Laravel, decoupling model lifecycle, training, and inference.

```
Training Pipeline:
  CSV Dataset -> Validation & Cleaning -> Feature Engineering -> Preprocessing Pipeline -> Model Comparison (LR, RF, GBDT, XGBoost) -> Metric Evaluation (MAE, RMSE, R2) -> Best Model Serialization -> Model Versioning

Inference Pipeline:
  JSON Request -> Preprocessing Pipeline (Same as training) -> Active Model Predict -> Confidence Interval / Price Range -> SHAP Influencing Factors -> JSON Response
```

## 2. Models Evaluated
- Linear Regression
- Decision Tree Regressor
- Random Forest Regressor
- Gradient Boosting Regressor
- XGBoost Regressor

The active model is loaded into memory at FastAPI service startup for sub-millisecond response latency.
