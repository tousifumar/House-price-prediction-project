# House Price Predictor — AI-Powered Real Estate Valuation SaaS Platform

[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![Python](https://img.shields.io/badge/Python-3.11%20%7C%203.12%20%7C%203.13-3776AB?logo=python&logoColor=white)](https://python.org)
[![FastAPI](https://img.shields.io/badge/FastAPI-0.110-009688?logo=fastapi&logoColor=white)](https://fastapi.tiangolo.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.4-38B2AC?logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.13-8BC0D0?logo=alpine.js&logoColor=white)](https://alpinejs.dev)
[![Chart.js](https://img.shields.io/badge/Chart.js-4.4-FF6384?logo=chart.js&logoColor=white)](https://chartjs.org)
[![Tests](https://img.shields.io/badge/Tests-39%20Passed%20(100%25)-success)](https://phpunit.de)

**House Price Predictor** is a production-ready, full-stack SaaS real estate valuation and property intelligence platform. It pairs a **Laravel 11** enterprise web application with a high-performance **Python FastAPI** microservice utilizing **Gradient Boosting Regressors** and **XGBoost** to deliver instant property valuations, dynamic 90% confidence intervals, and SHAP-aligned explainability breakdowns.

---

## 1. Key Features

### User & Investor Experience
* **Interactive AI Valuation Tool (`/predict`)**: Dynamic multi-variable input (Area, Carpet Area, BHK, Floor, Age, Furnishing, Amenities) with auto-calculated carpet guidelines and Leaflet.js interactive coordinate picker.
* **Valuation Results Dashboard (`/predictions/{id}`)**: Hero valuation estimate, 90% confidence range, price per sq.ft vs locality average, asking price deal verdict (Bargain vs Overpriced), and 12-month historical locality price trends.
* **Algorithmic Explainability**: Quantified positive (+) and negative (−) feature impacts detailing how floor rise, amenities, building age, and micro-market baseline shaped the estimate.
* **Executive PDF Valuation Dossiers (`/predictions/{id}/pdf`)**: Downloadable A4 valuation reports with reference IDs, methodology provenance, and statutory RERA disclaimers.
* **Prediction History (`/predictions`)**: Searchable and filterable archive of past valuation runs with pagination and quick export.
* **Saved Properties Portfolio (`/saved-properties`)**: Private portfolio tracking with personal investment notes and re-valuation triggers.
* **Property Comparison Matrix (`/compare`)**: Side-by-side spec comparison of 2 to 4 properties covering rate/sq.ft, BHK, age, amenities checklist, and location scores.
* **Market Analytics (`/market-analytics`)**: 4 Chart.js data visualizations (12-month price trajectory, ticket size by typology, micro-market rate comparisons, and annual capital appreciation velocity).
* **Price Alerts Manager (`/price-alerts`)**: Real-time market monitoring triggers notifying investors when valuations or listings dip below target budget thresholds.

### Admin Command Center (`/admin`)
* **Platform Operations Dashboard (`/admin`)**: High-level platform KPIs, active model accuracy, live valuation stream, and user metrics.
* **User Management (`/admin/users`)**: Search, role promotion/demotion (`admin`/`user`), suspension toggles, and deletion.
* **Property Catalog Management (`/admin/properties`)**: CRUD residential listings with relational amenities and automated coordinate binding.
* **Locality Benchmarks Management (`/admin/locations`)**: Adjust micro-market base rates per sq.ft, YoY growth rates, and location scores.
* **Amenity Catalog Management (`/admin/amenities`)**: Curate verified society amenities and toggle active status.
* **Dataset Management (`/admin/datasets`)**: Upload CSV datasets, validate column headers and record counts, and inspect first 10 rows in an interactive preview modal.
* **Model Registry & Retraining (`/admin/models`)**: Model version comparisons ($R^2$, MAE, RMSE, MAPE), one-click candidate promotion to production, and automated retraining launcher.

---

## 2. System Architecture

```
                                  Client Browser
                                        |
               +------------------------+------------------------+
               |                                                 |
         Web Interface                                      REST APIs
    (Blade / Tailwind / Alpine)                       (Sanctum Bearer Token)
               |                                                 |
               +------------------------+------------------------+
                                        |
                             Laravel 11 Backend
                        (Routing, Auth, DB Models,
                         Validation, Controllers)
                                        |
               +------------------------+------------------------+
               |                                                 |
       Database Layer                                   Internal REST Client
 (SQLite / MySQL 8.0 / Eloquent)                     (Guzzle HTTP / Timeout=15s)
               |                                                 |
     Relational Tables                                           v
 (Users, Properties, Localities,                      FastAPI ML Microservice
  Datasets, Predictions, Alerts)                       (Python 3.11+ / Uvicorn)
                                                                 |
                                                    +------------+------------+
                                                    |                         |
                                              Preprocessor              Active Model
                                            (StandardScaler /       (Gradient Boosting /
                                             OneHotEncoder)               XGBoost)
```

---

## 3. Technology Stack

| Layer | Technologies |
| :--- | :--- |
| **Backend Framework** | Laravel 11 (PHP 8.3+) |
| **Authentication** | Laravel Session Auth (Web) & Laravel Sanctum (REST API) |
| **Frontend UI** | Blade Templates, Vanilla CSS & Tailwind CSS, Alpine.js |
| **Visualizations** | Chart.js 4.4, Leaflet.js (OpenStreetMap) |
| **PDF Generation** | `barryvdh/laravel-dompdf` (DomPDF 3.1) |
| **Machine Learning** | Python 3.11+, Scikit-Learn 1.4+, XGBoost 2.0+, Pandas 2.2+, Joblib |
| **ML Microservice** | FastAPI, Uvicorn, Pydantic v2 |
| **Database** | SQLite (Default for zero-config run) / MySQL 8.0+ |

---

## 4. Default Demonstration Accounts

| Role | Email | Password | Access Level |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@houseprice.ai` | `password123` | Full Admin Command Center (`/admin`) |
| **Standard Investor** | `demo@houseprice.ai` | `password123` | Investor Dashboard, Valuations, Portfolio |

> 💡 *A **1-Click Demo Login** button is also available on `/login` for instant zero-credential evaluation.*

---

## 5. Quickstart & Installation

### Prerequisites
* PHP 8.2 or higher (with `pdo_sqlite`, `curl`, `mbstring`, `fileinfo`, `gd`, `zip` extensions enabled)
* Composer 2.x
* Node.js 18+ and NPM
* Python 3.11+ with `pip`

### Step 1: Clone and Setup Laravel Application
```bash
cd d:\AI-project\laravel-app

# Install PHP dependencies
composer install

# Environment configuration
cp .env.example .env
php artisan key:generate

# Run database migrations and seed preloaded Bangalore dataset
php artisan migrate:fresh --seed

# Install frontend dependencies and build assets
npm install
npm run build
```

### Step 2: Setup Python ML Service
```bash
cd d:\AI-project\ml-service

# Install Python requirements
pip install -r requirements.txt

# (Optional) Retrain models on Bangalore dataset
python training/train.py
```

### Step 3: Start Application Services

**Terminal 1 — Python ML Service (Port 8001):**
```bash
cd d:\AI-project\ml-service
uvicorn app.main:app --host 127.0.0.1 --port 8001
```

**Terminal 2 — Laravel Application (Port 8000):**
```bash
cd d:\AI-project\laravel-app
php artisan serve --port=8000
```

Open `http://127.0.0.1:8000` in your web browser.

---

## 6. Machine Learning Pipeline

The ML pipeline is located in `ml-service/`:

### Training Pipeline (`training/train.py`)
1. **Dataset**: 3,000 empirical Bangalore property transactions (`ml-service/datasets/bangalore_house_prices.csv`).
2. **Feature Engineering**:
   - `area_per_bedroom = area / bedrooms`
   - `bathroom_per_bedroom = bathrooms / bedrooms`
   - `floor_ratio = floor / total_floors`
   - `locality_base_rate` & `location_score` mapping
   - `amenities_count`
3. **Model Evaluation Results**:
   | Algorithm | $R^2$ Score | Mean Absolute Error (MAE) | Root Mean Squared Error (RMSE) | MAPE |
   | :--- | :--- | :--- | :--- | :--- |
   | **Linear Regression** | 0.9206 | ₹18.86 Lakhs | ₹28.20 Lakhs | 15.43% |
   | **Decision Tree** | 0.9491 | ₹14.07 Lakhs | ₹22.56 Lakhs | 8.91% |
   | **Random Forest** | 0.9787 | ₹9.43 Lakhs | ₹14.60 Lakhs | 6.28% |
   | **XGBoost** | 0.9847 | ₹7.98 Lakhs | ₹12.37 Lakhs | 5.04% |
   | **Gradient Boosting** | **0.9880** | **₹7.25 Lakhs** | **₹10.94 Lakhs** | **4.70%** |

The best model is saved to `ml-service/models/active_model.joblib` and hot-reloaded automatically.

---

## 7. REST API Reference

### 1. Predict Property Price
* **Endpoint**: `POST /api/predict`
* **Rate Limit**: 60 requests / minute
* **Request Payload**:
  ```json
  {
    "property_type": "Apartment",
    "transaction_type": "Sale",
    "city": "Bangalore",
    "locality": "Whitefield",
    "area": 1500,
    "carpet_area": 1250,
    "bedrooms": 3,
    "bathrooms": 2,
    "balconies": 2,
    "floor": 6,
    "total_floors": 14,
    "property_age": 3,
    "furnishing": "Semi-Furnished",
    "parking": true,
    "amenities": ["Gym", "Swimming Pool", "Clubhouse", "24x7 Security"],
    "user_asking_price": 14000000
  }
  ```
* **Response (HTTP 200)**:
  ```json
  {
    "success": true,
    "data": {
      "prediction_id": 14,
      "predicted_price": 13850000,
      "formatted_price": "₹1.39 Cr",
      "price_range": {
        "min": 13157500,
        "max": 14542500,
        "formatted": "₹1.32 Cr — ₹1.45 Cr"
      },
      "price_per_sqft": 9233,
      "confidence_score": 98.8,
      "currency": "INR",
      "asking_price_insight": "The seller asking price of ₹1.40 Cr is approximately fair market value (+1.1% variance).",
      "influencing_factors": [
        {
          "factor_name": "Location Baseline",
          "factor_type": "positive",
          "impact_amount": 3462500,
          "impact_percentage": 25.0,
          "description": "Premium micro-market corridor positioning in Whitefield"
        }
      ]
    }
  }
  ```

### 2. Location & Locality Directory
* `GET /api/locations`: Active cities and bounding coordinates.
* `GET /api/localities?city_id=1`: Locality benchmark rates, YoY growth rates, and location scores.
* `GET /api/amenities`: Catalog of 15 society amenities.

### 3. Property Search
* `GET /api/properties?locality_id=1&property_type=Apartment&bedrooms=3&min_price=10000000`

---

## 8. Automated Test Suite

Run the full automated test suite containing **39 feature and unit tests**:
```bash
cd d:\AI-project\laravel-app
php artisan test
```

### Test Coverage Summary
* **AuthTest**: Login, Registration, 1-click demo access, role authorization guards.
* **PredictionApiTest**: JSON structure validation, input constraints, asking price analyzer, factor attribution.
* **PredictionWebTest**: Form submission redirect, valuation dashboard rendering, PDF binary streaming, saved properties, comparison matrix, and price alert lifecycle.
* **AdminTest**: Administrator route guards, user suspension, property CRUD, locality benchmark updating, CSV dataset uploads, and model activation.
* **SecurityTest**: Rate limiting verification, XSS escaping, SQL injection resilience, and inactive account interception.

```
Tests:       39 passed (100%)
Assertions:  180 passed
Duration:    9.68s
Status:      100% GREEN
```

---

## 9. Production Deployment Guidelines

For production Linux deployments:

1. **Web Server**: Nginx with PHP 8.3-FPM (`fastcgi_pass unix:/var/run/php/php8.3-fpm.sock`).
2. **Python ML Daemon**: Managed via **Supervisor** running Uvicorn workers:
   ```ini
   [program:houseprice-ml]
   command=/var/www/houseprice/ml-service/venv/bin/uvicorn app.main:app --host 127.0.0.1 --port 8001 --workers 4
   directory=/var/www/houseprice/ml-service
   autostart=true
   autorestart=true
   user=www-data
   ```
3. **Optimizations**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   npm run build
   ```

---

## 10. License

This project is built and maintained as an enterprise real estate property valuation software solution. All rights reserved.
