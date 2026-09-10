# 📚 دليل APIs المطاعم والوجبات

## ✅ تم إصلاح جميع الأخطاء

### الأخطاء التي تم إصلاحها:
1. ✅ إضافة `Diet` model import في `RestaurantController`
2. ✅ تصحيح route لـ `getMealsByDiet` ليشير إلى `RestaurantController`
3. ✅ تحديث Seeder ليستخدم `updateOrInsert` لتجنب أخطاء التكرار

---

## 🆕 API الجديد: عرض المطاعم حسب الحمية

### **Endpoint:**
```
GET /api/restaurants/by-diet/{dietId}
```

### **الوصف:**
يعرض المطاعم مرتبة حسب عدد الوجبات المناسبة للحمية المحددة، مع إمكانية الترتيب حسب المسافة إذا تم توفير موقع المستخدم.

### **Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `dietId` | integer | ✅ Yes | معرف الحمية |
| `latitude` | float | ❌ No | خط العرض لموقع المستخدم |
| `longitude` | float | ❌ No | خط الطول لموقع المستخدم |
| `radius_km` | float | ❌ No | نصف القطر بالكيلومترات (default: 50) |

### **أمثلة الاستخدام:**

#### 1. بدون موقع المستخدم (ترتيب حسب عدد الوجبات فقط):
```
GET http://localhost:8000/api/restaurants/by-diet/4
```

#### 2. مع موقع المستخدم (ترتيب حسب الوجبات والمسافة):
```
GET http://localhost:8000/api/restaurants/by-diet/4?latitude=24.7136&longitude=46.6753
```

#### 3. مع موقع ونصف قطر محدد:
```
GET http://localhost:8000/api/restaurants/by-diet/4?latitude=24.7136&longitude=46.6753&radius_km=20
```

### **Response Example:**
```json
{
  "status": "success",
  "message": "تم جلب المطاعم بنجاح",
  "data": {
    "diet": {
      "id": 4,
      "name": "متوازن",
      "description": "نظام غذائي متوازن يحتوي على جميع العناصر الغذائية",
      "photo_url": "https://images.unsplash.com/photo-1498837167922-ddd27525d352?w=800"
    },
    "user_location": {
      "latitude": 24.7136,
      "longitude": 46.6753,
      "radius_km": 50
    },
    "restaurants": [
      {
        "id": 1,
        "name": "مطعم الصحة والعافية",
        "description": "مطعم متخصص في الوجبات الصحية والمتوازنة",
        "photo_url": "https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800",
        "state": "active",
        "diet_meals_count": 8,
        "total_meals_count": 12,
        "distance_km": 2.5,
        "phones": ["0501234567", "0501234568"],
        "locations": [
          {
            "id": 1,
            "latitude": 24.7136,
            "longitude": 46.6753,
            "description": "الرياض - حي العليا"
          }
        ]
      }
    ],
    "count": 3,
    "sorting": "مرتب حسب: عدد الوجبات المناسبة ثم المسافة"
  }
}
```

---

## 📋 APIs الموجودة (تم إصلاحها)

### **API 1: عرض الوجبات في مطعم محدد تناسب حمية محددة**
```
GET /api/restaurants/{restaurantId}/diets/{dietId}/meals
```
**مثال:**
```
GET http://localhost:8000/api/restaurants/1/diets/4/meals
```

---

### **API 2: عرض الوجبات في مطعم محدد وقسم محدد تناسب حمية محددة**
```
GET /api/restaurants/{restaurantId}/categories/{categoryId}/diets/{dietId}/meals
```
**مثال:**
```
GET http://localhost:8000/api/restaurants/1/categories/8/diets/4/meals
```

---

### **API 3: عرض تفاصيل وجبة محددة بجميع سماتها**
```
GET /api/meals/{id}/full-details
```
**مثال:**
```
GET http://localhost:8000/api/meals/1/full-details
```

---

### **API 4: عرض الوجبات حسب حمية محددة**
```
GET /api/diets/{dietId}/meals
```
**Parameters:**
- `restaurant_id` (optional)
- `proper_time` (optional): breakfast, lunch, dinner
- `sort_by` (optional): name, price, calories
- `sort_order` (optional): asc, desc
- `per_page` (optional): عدد النتائج في الصفحة

**مثال:**
```
GET http://localhost:8000/api/diets/4/meals?restaurant_id=1&proper_time=lunch&sort_by=price&sort_order=asc
```

---

## 🗄️ إضافة البيانات الافتراضية

### **تشغيل Seeder:**
```bash
php artisan db:seed --class=TestDataSeeder
```

### **البيانات التي سيتم إضافتها:**
- ✅ 5 حميات (كيتو، نباتي، خالي من الجلوتين، **متوازن**، عالي البروتين)
- ✅ 3 مطاعم
- ✅ 8 فئات (فطور، غداء، عشاء، سلطات، حلويات، مشروبات، وجبات خفيفة، **أطباق رئيسية**)
- ✅ 12 وجبة متنوعة
- ✅ علاقات بين الوجبات والحميات
- ✅ أرقام هواتف ومواقع المطاعم

**ملاحظة:** الـ Seeder يستخدم `updateOrInsert` الآن، لذا يمكن تشغيله عدة مرات بدون أخطاء تكرار.

---

## 🎯 حالة الاستخدام الرئيسية

### **صفحة عرض المطاعم حسب الحمية:**

1. **المستخدم يختار حمية من الأعلى** (مثلاً: متوازن - ID: 4)

2. **استدعاء API:**
```
GET /api/restaurants/by-diet/4?latitude=24.7136&longitude=46.6753
```

3. **النتيجة:**
   - المطاعم مرتبة حسب:
     - **أولاً:** عدد الوجبات المناسبة للحمية (الأكثر أولاً)
     - **ثانياً:** المسافة من موقع المستخدم (الأقرب أولاً)
   
4. **البيانات المعروضة لكل مطعم:**
   - الاسم والوصف
   - الصورة
   - عدد الوجبات المناسبة للحمية
   - إجمالي عدد الوجبات
   - المسافة (إذا كان الموقع متوفراً)
   - أرقام الهواتف
   - المواقع

---

## 🔧 ملاحظات مهمة

1. **جميع الصور من Unsplash** - موقع موثوق للصور المجانية عالية الجودة
2. **الـ APIs تدعم التصفية والترتيب** المتقدم
3. **الأخطاء السابقة تم إصلاحها بالكامل**
4. **البيانات الافتراضية جاهزة للاختبار**

---

## ✨ الخطوات التالية

1. شغّل الـ Seeder:
```bash
php artisan db:seed --class=TestDataSeeder
```

2. اختبر الـ API الجديد:
```bash
curl "http://localhost:8000/api/restaurants/by-diet/4"
```

3. اختبر مع الموقع:
```bash
curl "http://localhost:8000/api/restaurants/by-diet/4?latitude=24.7136&longitude=46.6753"
```
