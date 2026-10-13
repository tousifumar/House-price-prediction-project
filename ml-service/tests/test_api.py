"""
Python ML Microservice Automated Tests
"""
import requests

BASE_URL = "http://127.0.0.1:8001"

def test_health():
    res = requests.get(f"{BASE_URL}/health")
    assert res.status_code == 200, f"Health check failed: {res.text}"
    data = res.json()
    assert data["status"] == "healthy"
    assert data["model_loaded"] is True
    print("[PASS] Health endpoint passed")

def test_model_info():
    res = requests.get(f"{BASE_URL}/model-info")
    assert res.status_code == 200, f"Model info failed: {res.text}"
    data = res.json()
    assert "algorithm" in data
    assert "r2_score" in data
    assert data["r2_score"] > 0.90
    print(f"[PASS] Model info passed ({data['algorithm']}, R2: {data['r2_score']})")

def test_predict():
    payload = {
        "property_type": "Apartment",
        "transaction_type": "Sale",
        "city": "Bangalore",
        "locality": "Whitefield",
        "area": 1500,
        "carpet_area": 1300,
        "bedrooms": 3,
        "bathrooms": 2,
        "balconies": 2,
        "floor": 8,
        "total_floors": 15,
        "property_age": 5,
        "furnishing": "Semi-Furnished",
        "parking": True,
        "amenities": ["Gym", "Lift", "Security"]
    }
    res = requests.post(f"{BASE_URL}/predict", json=payload)
    assert res.status_code == 200, f"Predict failed: {res.text}"
    data = res.json()
    assert data["success"] is True
    assert data["predicted_price"] > 0
    assert "price_range" in data
    assert data["price_range"]["min"] < data["predicted_price"] < data["price_range"]["max"]
    assert len(data["influencing_factors"]) > 0
    formatted_p = data['formatted_price'].replace('₹', 'INR ')
    formatted_r = data['price_range']['formatted'].replace('₹', 'INR ')
    print(f"[PASS] Predict passed! Predicted: {formatted_p}, Range: {formatted_r}")
    print(f"  Confidence: {data['confidence_score']}%, Factors count: {len(data['influencing_factors'])}")

if __name__ == "__main__":
    test_health()
    test_model_info()
    test_predict()
    print("\nAll Python ML API tests passed successfully!")
