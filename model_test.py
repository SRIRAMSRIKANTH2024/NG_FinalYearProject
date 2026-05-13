
import cv2
import numpy as np
import tensorflow as tf

BASE = r"D:\Xampp\htdocs\neighbourguard\Models"

fire_model         = tf.keras.models.load_model(f"{BASE}\\fire_detection_model.h5")
stray_animal_model = tf.keras.models.load_model(f"{BASE}\\stray_animal_model.h5")

MODELS = [
    (fire_model,         "🔥 Fire",        "FIRE detected!",         "No fire"),
    (stray_animal_model, "🐕 Stray Animal", "STRAY ANIMAL detected!", "No stray animal"),
]

def predict(model, image_path, label_yes, label_no):
    img = cv2.imread(image_path)
    img = cv2.resize(img, (224, 224))
    img = img / 255.0
    img = np.expand_dims(img, axis=0)
    result = model.predict(img)[0][0]
    label = label_yes if result > 0.5 else label_no
    print(f"   → {label} (confidence: {result:.2f})")

def analyze_image(image_path):
    print(f"\n📸 Analyzing: {image_path}")
    print("-" * 40)
    for model, name, yes, no in MODELS:
        print(f"{name}:")
        predict(model, image_path, yes, no)
    print("-" * 40)

analyze_image("dog.jpg")
analyze_image("cat.jpg")
analyze_image("fire1.jpg")
analyze_image("fire3.jpg")
