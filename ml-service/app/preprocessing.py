"""
Data Preprocessing Pipeline for House Price Prediction
Matching Section 18 & 19 of specification:
- Handling missing values and outliers
- Derived feature engineering
- Categorical encoding and numerical scaling
- Reproducible Scikit-Learn pipeline
"""
import os
import joblib
import numpy as np
import pandas as pd
from sklearn.compose import ColumnTransformer
from sklearn.preprocessing import OneHotEncoder, StandardScaler

# Locality benchmark metadata
LOCALITY_METRIC_MAP = {
    'Whitefield': {'base_rate': 8400, 'score': 9.1},
    'Electronic City': {'base_rate': 5800, 'score': 8.6},
    'HSR Layout': {'base_rate': 11200, 'score': 9.4},
    'Koramangala': {'base_rate': 13500, 'score': 9.6},
    'Indiranagar': {'base_rate': 15000, 'score': 9.7},
    'Sarjapur Road': {'base_rate': 7900, 'score': 9.0},
    'Marathahalli': {'base_rate': 7200, 'score': 8.7},
    'Hebbal': {'base_rate': 10400, 'score': 9.2},
    'Yelahanka': {'base_rate': 6900, 'score': 8.8},
    'Bannerghatta Road': {'base_rate': 7100, 'score': 8.7},
}

DEFAULT_RATE = 8000
DEFAULT_SCORE = 8.5

NUMERICAL_FEATURES = [
    'area',
    'carpet_area',
    'bedrooms',
    'bathrooms',
    'balconies',
    'floor',
    'total_floors',
    'property_age',
    'amenities_count',
    'parking',
    'area_per_bedroom',
    'bathroom_per_bedroom',
    'floor_ratio',
    'locality_base_rate',
    'location_score',
]

CATEGORICAL_FEATURES = [
    'locality',
    'property_type',
    'furnishing',
]

def engineer_features(df: pd.DataFrame) -> pd.DataFrame:
    """Creates derived features matching Section 19."""
    data = df.copy()

    # Default missing fields if necessary
    if 'parking' not in data.columns:
        data['parking'] = 1
    if 'amenities_count' not in data.columns:
        data['amenities_count'] = 5
    if 'carpet_area' not in data.columns or data['carpet_area'].isnull().all():
        data['carpet_area'] = data['area'] * 0.82
    else:
        data['carpet_area'] = data['carpet_area'].fillna(data['area'] * 0.82)

    # Convert types
    data['parking'] = data['parking'].astype(int)
    data['bedrooms'] = np.maximum(1, data['bedrooms'].astype(int))
    data['bathrooms'] = np.maximum(1, data['bathrooms'].astype(int))
    data['total_floors'] = np.maximum(1, data['total_floors'].astype(int))

    # Derived features
    data['area_per_bedroom'] = data['area'] / data['bedrooms']
    data['bathroom_per_bedroom'] = data['bathrooms'] / data['bedrooms']
    data['floor_ratio'] = data['floor'] / data['total_floors']

    # Locality mapped features
    def get_rate(loc):
        return LOCALITY_METRIC_MAP.get(str(loc), {}).get('base_rate', DEFAULT_RATE)

    def get_score(loc):
        return LOCALITY_METRIC_MAP.get(str(loc), {}).get('score', DEFAULT_SCORE)

    data['locality_base_rate'] = data['locality'].apply(get_rate)
    data['location_score'] = data['locality'].apply(get_score)

    return data

def build_preprocessor() -> ColumnTransformer:
    """Builds a scikit-learn ColumnTransformer for numerical and categorical variables."""
    numeric_transformer = StandardScaler()
    categorical_transformer = OneHotEncoder(handle_unknown='ignore', sparse_output=False)

    preprocessor = ColumnTransformer(
        transformers=[
            ('num', numeric_transformer, NUMERICAL_FEATURES),
            ('cat', categorical_transformer, CATEGORICAL_FEATURES),
        ],
        remainder='drop',
    )
    return preprocessor

class PreprocessingPipeline:
    def __init__(self):
        self.preprocessor = build_preprocessor()
        self.is_fitted = False
        self.feature_names_out = []

    def fit(self, df: pd.DataFrame):
        engineered = engineer_features(df)
        self.preprocessor.fit(engineered)
        self.is_fitted = True
        self.feature_names_out = self.preprocessor.get_feature_names_out().tolist()
        return self

    def transform(self, df: pd.DataFrame) -> np.ndarray:
        if not self.is_fitted:
            raise ValueError("PreprocessingPipeline must be fitted before transforming.")
        engineered = engineer_features(df)
        return self.preprocessor.transform(engineered)

    def fit_transform(self, df: pd.DataFrame) -> np.ndarray:
        self.fit(df)
        return self.transform(df)

    def save(self, filepath: str):
        os.makedirs(os.path.dirname(filepath), exist_ok=True)
        joblib.dump(self, filepath)

    @classmethod
    def load(cls, filepath: str) -> 'PreprocessingPipeline':
        return joblib.load(filepath)
