# House Price Predictor — System Architecture

## 1. High-Level Architecture Overview

The platform uses a decoupled, service-oriented architecture:

```
                                  Client Browser
                                        |
                                        v
                    +---------------------------------------+
                    |        Laravel 11 Frontend/API       |
                    | (Blade, Tailwind CSS, Alpine, Charts) |
                    +---------------------------------------+
                                        |
               +------------------------+------------------------+
               |                                                 |
               v                                                 v
    +-----------------------+                         +-----------------------+
    |     SQLite / MySQL    |                         |  FastAPI ML Service   |
    |  Relational Storage   |                         |  (Python 3.11+ / 3.13)|
    +-----------------------+                         +-----------------------+
               |                                                 |
    - Users, Roles & Auth                              - Data Preprocessor
    - Predictions & Factors                            - Active Scikit/XGBoost
    - Properties & Amenities                           - SHAP Explainability
    - Locality Market Data                             - Model Registry
    - Datasets & ML Models                             - Dynamic Retraining
```

## 2. Responsibilities
- **Laravel Backend**: Authentication, Authorization, Form Validation, Eloquent Persistence, History, Comparison, PDF Generation, Price Alerts, Scheduled Monitoring, Admin Management.
- **Python FastAPI Service**: In-memory trained model inference, SHAP feature importance calculation, model training pipeline, evaluation metrics (MAE, RMSE, R²).
- **Communication**: Resilient HTTP REST calls with configurable timeouts, retries, and graceful fallbacks.
