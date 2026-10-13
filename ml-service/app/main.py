"""
FastAPI Microservice for House Price Prediction
Matches Section 16 of the specification.
"""
from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from app.model_loader import model_manager
from app.predictor import predict_property
from app.schemas import (
    PropertyPredictionRequest,
    PredictionResponse,
    ModelInfoResponse,
    HealthResponse,
)

app = FastAPI(
    title="House Price Predictor — ML Microservice",
    description="Real-estate valuation and property price prediction inference API",
    version="1.0.0",
)

# Enable CORS for local cross-service communication
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

@app.get("/health", response_model=HealthResponse)
def health():
    mgr = model_manager
    return HealthResponse(
        status="healthy",
        service="Python FastAPI ML Service",
        model_loaded=(mgr.model is not None),
        version=mgr.metadata.get("version", "v1.0"),
    )

@app.get("/model-info", response_model=ModelInfoResponse)
def model_info():
    mgr = model_manager
    meta = mgr.metadata
    features = list(meta.get("top_feature_importance", {}).keys())

    return ModelInfoResponse(
        status=meta.get("status", "active"),
        version=meta.get("version", "v1.0-xgb"),
        algorithm=meta.get("algorithm", "Gradient Boosting Regressor"),
        r2_score=meta.get("best_r2_score", 0.988),
        mae=meta.get("best_mae", 725032.0),
        rmse=meta.get("best_rmse", 1094437.0),
        mape=meta.get("best_mape", 0.047),
        trained_at=meta.get("trained_at", "preloaded"),
        features=features,
    )

@app.post("/predict", response_model=PredictionResponse)
def predict(request: PropertyPredictionRequest):
    try:
        return predict_property(request)
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Inference error: {str(e)}")

@app.post("/reload-model")
def reload_model():
    return model_manager.reload()

if __name__ == "__main__":
    import uvicorn
    uvicorn.run("app.main:app", host="127.0.0.1", port=8001, reload=False)
