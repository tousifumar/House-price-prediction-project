# Python ML Microservice — House Price Predictor

This microservice provides high-throughput real-estate price prediction and explainability endpoints using FastAPI, Scikit-learn, and XGBoost.

## Endpoints:
- `POST /predict`: Generate price estimation, confidence interval, and factor impacts.
- `GET /health`: Microservice health check.
- `GET /model-info`: Active model metadata and metrics.
- `POST /reload-model`: Hot-reload newly trained model weights without downtime.
