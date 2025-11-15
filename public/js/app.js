// =================================================================
// 1. Data Definitions (Meals & Translations)
// =================================================================

// Data structure for meals (for demonstration purposes)
const mealsData = [
    {
        id: 1,
        title: "Grilled Salmon with Quinoa",
        description: "A perfect blend of lean protein and complex carbohydrates, rich in Omega-3 fatty acids.",
        calories: 450, protein: 35, carbs: 40, fat: 18,
        image: "images/meal_1.jpg",
        ingredients: [
            { name: "Salmon Fillet (150g)", checked: true },
            { name: "Quinoa (1 cup cooked)", checked: true },
            { name: "Roasted Asparagus", checked: true },
            { name: "Lemon Wedge", checked: true },
            { name: "Olive Oil (1 tbsp)", checked: true }
        ]
    },
    {
        id: 2,
        title: "Rainbow Veggie Buddha Bowl",
        description: "A vibrant and nutrient-dense vegan bowl packed with vitamins and fiber.",
        calories: 380, protein: 15, carbs: 55, fat: 10,
        image: "images/meal_2.jpg",
        ingredients: [
            { name: "Brown Rice (1 cup)", checked: true },
            { name: "Roasted Sweet Potatoes", checked: true },
            { name: "Avocado Slices", checked: true },
            { name: "Chickpeas", checked: true },
            { name: "Kale and Spinach Mix", checked: true }
        ]
    },
    {
        id: 3,
        title: "Mediterranean Chicken Salad",
        description: "Light and refreshing salad with grilled chicken, feta, and a lemon-herb dressing.",
        calories: 320, protein: 30, carbs: 20, fat: 15,
        image: "images/meal_3.jpg",
        ingredients: [
            { name: "Grilled Chicken Breast", checked: true },
            { name: "Mixed Greens", checked: true },
            { name: "Feta Cheese (low-fat)", checked: true },
            { name: "Cucumber and Tomato", checked: true },
            { name: "Olive Oil Vinaigrette", checked: true }
        ]
    },
    {
        id: 4,
        title: "Lean Steak with Roasted Veggies",
        description: "A satisfying meal for muscle building, providing high-quality protein and essential minerals.",
        calories: 510, protein: 45, carbs: 30, fat: 22,
        image: "images/meal_4.jpg",
        ingredients: [
            { name: "Lean Beef Steak (150g)", checked: true },
            { name: "Roasted Broccoli and Carrots", checked: true },
            { name: "Small Baked Potato", checked: true },
            { name: "Herbs and Spices", checked: true }
        ]
    },
    {
        id: 5,
        title: "Hearty Lentil Soup",
        description: "A warm, high-fiber, and satisfying vegetarian soup.",
        calories: 250, protein: 18, carbs: 35, fat: 5,
        image: "images/meal_1.jpg", // Placeholder image
        ingredients: [
            { name: "Lentils", checked: true },
            { name: "Carrots", checked: true },
            { name: "Celery", checked: true },
            { name: "Vegetable Broth", checked: true },
            { name: "Spices", checked: true }
        ]
    },
    {
        id: 6,
        title: "Whole Wheat Tuna Wrap",
        description: "A quick and light lunch option packed with lean protein.",
        calories: 350, protein: 25, carbs: 30, fat: 10,
        image: "images/meal_2.jpg", // Placeholder image
        ingredients: [
            { name: "Whole Wheat Tortilla", checked: true },
            { name: "Tuna (canned in water)", checked: true },
            { name: "Light Mayo", checked: true },
            { name: "Lettuce and Tomato", checked: true }
        ]
    },
    {
        id: 101,
        title: "Grilled Salmon with Asparagus",
        description: "A high-protein, low-carb meal.",
        calories: 420, protein: 40, carbs: 5, fat: 25,
        image: "images/meal_1.jpg",
        ingredients: [
            { name: "Salmon fillet", checked: true },
            { name: "Asparagus", checked: true },
            { name: "Lemon", checked: true },
            { name: "Olive Oil", checked: true }
        ]
    },
    {
        id: 102,
        title: "Chicken Quinoa Bowl",
        description: "Balanced meal with complex carbs and lean protein.",
        calories: 480, protein: 38, carbs: 45, fat: 15,
        image: "images/meal_2.jpg",
        ingredients: [
            { name: "Chicken breast", checked: true },
            { name: "Quinoa", checked: true },
            { name: "Black Beans", checked: true },
            { name: "Corn", checked: true },
            { name: "Avocado", checked: true }
        ]
    },
    {
        id: 103,
        title: "Vegetarian Lentil Soup",
        description: "High in fiber and vegetarian protein.",
        calories: 300, protein: 15, carbs: 50, fat: 5,
        image: "images/meal_3.jpg",
        ingredients: [
            { name: "Lentils", checked: true },
            { name: "Carrots", checked: true },
            { name: "Celery", checked: true },
            { name: "Vegetable Broth", checked: true },
            { name: "Spices", checked: true }
        ]
    }
];

// ==================== MASTER TRANSLATIONS OBJECT ====================
const translations = {
    en: {
        // Nav & General UI
        dashboard: "Dashboard",
        diets: "Diets",
        dishes: "Dishes",
        users: "Users",
        notifications: "Notifications",
        searchPlaceholder: "Search here...",
        hiSamantha: "Hi, Samantha",
        back: "Back",
        logo: "MealMate",
        youAreHere: "You are here to change our lives for the better",

        // Page Titles & Management
        userManagement: "User Management",
        addNewClient: "Add New Client",
        editClient: "Edit Client",
        clientProfileTitle: "Client Profile",
        notificationsTitle: "Notifications",
        addNewDiet: "Add New Diet",
        healthyDishes: "Healthy Dishes",
        recentMeals: "Recent Meals",
        recentMealsFull: "الوجبات المضافة مؤخراً", // Matched key from AR

        // Buttons & Actions
        addNew: "+ Add New",
        save: "Save",
        cancel: "Cancel",
        delete: "Delete",
        edit: "Edit",
        addInfo: "Add Info",
        addNewMeal: "+ Add New Meal",
        checkAdd: "Check (Add)",
        falseReject: "False (Reject)",
        saveChanges: "Save Changes",
        confirmMeals: "Confirm Selected Meals",

        // User Table Headers
        clientName: "Client Name",
        email: "Email",
        phone: "Phone",
        dietPlan: "Diet Plan",
        adherence: "Adherence",
        actions: "Actions",

        // User Form Fields
        basicInfo: "Basic Information",
        fullName: "Full Name:",
        address: "Address:",
        gender: "Gender:",
        male: "Male",
        female: "Female",
        dob: "Date of Birth:",

        // Client Profile
        healthMetrics: "Health Metrics",
        dietHistory: "Diet History",
        currentDietPlan: "Current Diet Plan",

        // Notifications
        newDietsAdded: "New diets were added to the system.",
        paymentReceived: "Payment received from Ahmed.",
        weeklyReportReady: "Your weekly adherence report is ready.",

        // Diet/Dishes Pages
        mealsInDiet: "Meals in Mediterranean Diet",
        mediterraneanDiet: "Mediterranean Diet",
        mediterraneanDescription: "The Mediterranean Diet is a way of eating inspired by the traditional diets of countries bordering the Mediterranean Sea...", // (Shortened for brevity)
        adviceTitle: "Advice for People Starting the Mediterranean Diet",
        adviceText: "Swap butter for olive oil, eat more fruits and vegetables...",
        riceWithVegetables: "Rice with Vegetables",
        dietRestaurant: "Diet Restaurant",
        mealCardFooter: "Added to the diet",

        // Modal
        description: "Description:",
        nutritionalFacts: "Nutritional Facts:",
        components: "Components (Check to Modify):",

        // Toasts & Alerts
        addSuccessfully: "Add successfully",
        deleteConfirm: "Are you sure you want to reject/delete this meal?",
        dietDeleted: "Meal Deleted", // Changed from "Diet deleted" to match
        mealsAddedSuccess: "Meals added successfully!",

        // Dashboard
        patientsAdherence: "Patients' Adherence to Diets",
        totalOrder: "Total Order",
        customerGrowth: "Customer Growth",
        totalRevenue: "Total Revenue",

        // Doctor Profile
        drAfnanHomude: "Dr. Afnan Homude",
        drAfnanTitle: "Dr. Afnan Hamid – Clinical Nutritionist",
        drAfnanPoint1: "Licensed and experienced clinical nutritionist",
        drAfnanPoint2: "Specializes in personalized, science-based nutrition plans",
        drAfnanPoint3: "Offers expert guidance in weight management, therapeutic diets, and healthy lifestyle changes",
        drAfnanPoint4: "Committed to improving clients' health through simple, realistic, and sustainable strategies",
        drAfnanPoint5: "Known for a caring, supportive, and results-driven approach",
        drAfnanPoint6: "Provides online consultations tailored to each individual's needs and goals",
        
        // Meal Titles & Descriptions from Data
        "Grilled Salmon with Quinoa": "Grilled Salmon with Quinoa",
        "A perfect blend of lean protein and complex carbohydrates, rich in Omega-3 fatty acids.": "A perfect blend of lean protein and complex carbohydrates, rich in Omega-3 fatty acids.",
        "Rainbow Veggie Buddha Bowl": "Rainbow Veggie Buddha Bowl",
        "A vibrant and nutrient-dense vegan bowl packed with vitamins and fiber.": "A vibrant and nutrient-dense vegan bowl packed with vitamins and fiber.",
        "Mediterranean Chicken Salad": "Mediterranean Chicken Salad",
        "Light and refreshing salad with grilled chicken, feta, and a lemon-herb dressing.": "Light and refreshing salad with grilled chicken, feta, and a lemon-herb dressing.",
        "Lean Steak with Roasted Veggies": "Lean Steak with Roasted Veggies",
        "A satisfying meal for muscle building, providing high-quality protein and essential minerals.": "A satisfying meal for muscle building, providing high-quality protein and essential minerals.",
        "Hearty Lentil Soup": "Hearty Lentil Soup",
        "A warm, high-fiber, and satisfying vegetarian soup.": "A warm, high-fiber, and satisfying vegetarian soup.",
        "Whole Wheat Tuna Wrap": "Whole Wheat Tuna Wrap",
        "A quick and light lunch option packed with lean protein.": "A quick and light lunch option packed with lean protein.",
        "Grilled Salmon with Asparagus": "Grilled Salmon with Asparagus",
        "A high-protein, low-carb meal.": "A high-protein, low-carb meal.",
        "Chicken Quinoa Bowl": "Chicken Quinoa Bowl",
        "Balanced meal with complex carbs and lean protein.": "Balanced meal with complex carbs and lean protein.",
        "Vegetarian Lentil Soup": "Vegetarian Lentil Soup",
        "High in fiber and vegetarian protein.": "High in fiber and vegetarian protein.",

        // Recent Meals Components
        "Components: Salmon fillet, Asparagus, Lemon, Olive Oil.": "Components: Salmon fillet, Asparagus, Lemon, Olive Oil.",
        "Components: Chicken breast, Quinoa, Black Beans, Corn, Avocado.": "Components: Chicken breast, Quinoa, Black Beans, Corn, Avocado.",
        "Components: Lentils, Carrots, Celery, Vegetable Broth, Spices.": "Components: Lentils, Carrots, Celery, Vegetable Broth, Spices.",
    },
    ar: {
        // Nav & General UI
        dashboard: "لوحة التحكم",
        diets: "الحميات",
        dishes: "الأطباق",
        users: "المستخدمون",
        notifications: "الإشعارات",
        searchPlaceholder: "ابحث هنا...",
        hiSamantha: "مرحبا، سامانثا",
        back: "رجوع",
        logo: "ميل ميت",
        youAreHere: "أنت هنا لتغيير حياتنا إلى الأفضل",

        // Page Titles & Management
        userManagement: "إدارة المستخدمين",
        addNewClient: "إضافة عميل جديد",
        editClient: "تعديل بيانات العميل",
        clientProfileTitle: "الملف الشخصي للعميل",
        notificationsTitle: "الإشعارات",
        addNewDiet: "إضافة حمية جديدة",
        healthyDishes: "الأطباق الصحية",
        recentMeals: "الوجبات المضافة مؤخراً", // For button
        recentMealsFull: "الوجبات المضافة مؤخراً", // For title

        // Buttons & Actions
        addNew: "+ إضافة جديد",
        save: "حفظ",
        cancel: "إلغاء",
        delete: "حذف",
        edit: "تعديل",
        addInfo: "إضافة معلومات",
        addNewMeal: "+ إضافة وجبة جديدة",
        checkAdd: "تحقق (إضافة)",
        falseReject: "خطأ (رفض)",
        saveChanges: "حفظ التغييرات",
        confirmMeals: "تأكيد الوجبات المختارة",

        // User Table Headers
        clientName: "اسم العميل",
        email: "البريد الإلكتروني",
        phone: "الهاتف",
        dietPlan: "خطة الحمية",
        adherence: "الالتزام",
        actions: "الإجراءات",

        // User Form Fields
        basicInfo: "المعلومات الأساسية",
        fullName: "الاسم الكامل:",
        address: "العنوان:",
        gender: "الجنس:",
        male: "ذكر",
        female: "أنثى",
        dob: "تاريخ الميلاد:",

        // Client Profile
        healthMetrics: "المقاييس الصحية",
        dietHistory: "سجل الحميات",
        currentDietPlan: "خطة الحمية الحالية",

        // Notifications
        newDietsAdded: "تمت إضافة حميات جديدة إلى النظام.",
        paymentReceived: "تم استلام دفعة من أحمد.",
        weeklyReportReady: "تقرير الالتزام الأسبوعي الخاص بك جاهز.",

        // Diet/Dishes Pages
        mealsInDiet: "وجبات الحمية المتوسطية",
        mediterraneanDiet: "الحمية المتوسطية",
        mediterraneanDescription: "الحمية المتوسطية هي أسلوب غذائي مستوحى من الحميات التقليدية للبلدان المطلة على البحر الأبيض المتوسط...",
        adviceTitle: "نصائح للأشخاص الذين يبدأون الحمية المتوسطية",
        adviceText: "استبدل الزبدة بزيت الزيتون، وتناول المزيد من الفواكه والخضروات...",
        riceWithVegetables: "أرز بالخضروات",
        dietRestaurant: "مطعم الحمية",
        mealCardFooter: "تمت إضافتها للحمية",

        // Modal
        description: "الوصف:",
        nutritionalFacts: "الحقائق الغذائية:",
        components: "المكونات (حدد للتعديل):",

        // Toasts & Alerts
        addSuccessfully: "تمت الإضافة بنجاح",
        deleteConfirm: "هل أنت متأكد أنك تريد حذف هذه الوجبة؟",
        dietDeleted: "تم حذف الوجبة",
        mealsAddedSuccess: "تمت إضافة الوجبات بنجاح!",

        // Dashboard
        patientsAdherence: "التزام المرضى بالحميات",
        totalOrder: "إجمالي الطلب",
        customerGrowth: "نمو العملاء",
        totalRevenue: "إجمالي الإيرادات",

        // Doctor Profile
        drAfnanHomude: "د. أفنان حمود",
        drAfnanTitle: "د. أفنان حامد - أخصائية تغذية سريرية",
        drAfnanPoint1: "أخصائية تغذية سريرية مرخصة وذات خبرة عملية",
        drAfnanPoint2: "متخصصة في خطط التغذية المخصصة والقائمة على العلم",
        drAfnanPoint3: "توفر إرشادات خبيرة في إدارة الوزن والحميات العلاجية والتغييرات الصحية في نمط الحياة",
        drAfnanPoint4: "ملتزمة بتحسين صحة العملاء من خلال استراتيجيات بسيطة وواقعية وقابلة للاستدامة",
        drAfnanPoint5: "معروفة بنهج رعاية وداعم وموجه نحو النتائج",
        drAfnanPoint6: "توفر استشارات عبر الإنترنت مخصصة لاحتياجات وأهداف كل فرد",

        // Meal Titles & Descriptions from Data
        "Grilled Salmon with Quinoa": "سلمون مشوي مع الكينوا",
        "A perfect blend of lean protein and complex carbohydrates, rich in Omega-3 fatty acids.": "مزيج مثالي من البروتين الخفيف والكربوهرات المعقدة، وغني بأحماض أوميغا 3 الدهنية.",
        "Rainbow Veggie Buddha Bowl": "وعاء بوذا الخضري المتنوع",
        "A vibrant and nutrient-dense vegan bowl packed with vitamins and fiber.": "وعاء نباتي نابض بالحياة وغني بالعناصر الغذائية ومليء بالفيتامينات والألياف.",
        "Mediterranean Chicken Salad": "سلطة الدجاج المتوسطية",
        "Light and refreshing salad with grilled chicken, feta, and a lemon-herb dressing.": "سلطة خفيفة ومنعشة مع دجاج مشوي، جبنة فيتا، وتتبيلة ليمون وأعشاب.",
        "Lean Steak with Roasted Veggies": "شريحة لحم خفيفة مع الخضار المشوية",
        "A satisfying meal for muscle building, providing high-quality protein and essential minerals.": "وجبة مشبعة لبناء العضلات، توفر بروتين عالي الجودة ومعادن أساسية.",
        "Hearty Lentil Soup": "شوربة العدس الشهية",
        "A warm, high-fiber, and satisfying vegetarian soup.": "شوربة نباتية دافئة، عالية الألياف، ومشبعة.",
        "Whole Wheat Tuna Wrap": "لفافة التونة بالقمح الكامل",
        "A quick and light lunch option packed with lean protein.": "خيار غداء سريع وخفيف مليء بالبروتين الخفيف.",
        "Grilled Salmon with Asparagus": "سلمون مشوي مع الهليون",
        "A high-protein, low-carb meal.": "وجبة عالية البروتين ومنخفضة الكربوهيدرات.",
        "Chicken Quinoa Bowl": "وعاء الدجاج والكينوا",
        "Balanced meal with complex carbs and lean protein.": "وجبة متوازنة بالكربوهيدرات المعقدة والبروتين الخفيف.",
        "Vegetarian Lentil Soup": "شوربة العدس النباتية",
        "High in fiber and vegetarian protein.": "غنية بالألياف والبروتين النباتي.",

        // Recent Meals Components
        "Components: Salmon fillet, Asparagus, Lemon, Olive Oil.": "المكونات: قطعة سلمون، هليون، ليمون، زيت زيتون.",
        "Components: Chicken breast, Quinoa, Black Beans, Corn, Avocado.": "المكونات: صدر دجاج، كينوا، فاصوليا سوداء، ذرة، أفوكادو.",
        "Components: Lentils, Carrots, Celery, Vegetable Broth, Spices.": "المكونات: عدس، جزر، كرفس، مرق خضار، بهارات.",
    }
};


// =================================================================
// 2. Core Language & UI Management
// =================================================================

let currentLanguage = localStorage.getItem('language') || 'en';
const FIXED_DIR = 'ltr'; // *** تثبيت الاتجاه على LTR دائماً ***

/**
 * يترجم مفتاحاً بناءً على اللغة الحالية
 * @param {string} key - المفتاح المراد ترجمته
 * @returns {string} - النص المترجم
 */
function t(key) {
    return (translations[currentLanguage] && translations[currentLanguage][key] !== undefined)
        ? translations[currentLanguage][key]
        : (translations['en'][key] || key); // Return the key itself if no translation is found
}

/**
 * يطبّق اللغة المحددة على الصفحة
 * @param {string} lang - 'en' or 'ar'
 */
function setLanguage(lang) {
    currentLanguage = lang;
    localStorage.setItem('language', lang);

    // فرض اتجاه LTR دائماً
    document.documentElement.lang = lang;
    document.documentElement.dir = FIXED_DIR;
    document.body.dir = FIXED_DIR;

    // فرض محاذاة البحث لليسار
    document.querySelectorAll('.search-bar input').forEach(input => {
        input.style.cssText = 'text-align: left !important;';
    });
    // فرض اتجاه أيقونة الرجوع
    document.querySelectorAll('.back-button i, .back-btn i').forEach(icon => {
        icon.style.transform = 'rotate(0deg) !important';
    });

    updatePageContent();
    updateLanguageButtons();
}

/**
 * يحدّث جميع أجزاء المحتوى على الصفحة
 */
function updatePageContent() {
    // 1. ترجمة العناصر العامة باستخدام data-i18n
    translateGenericElements();

    // 2. تحديثات خاصة
    updateNavigation();
    updateGeneralUI();
    updateUserTableAndForms();
    updateDietsContent();
    updateRecentMealsContent();

    // 3. تحديث المحتوى الديناميكي (مثل المودال إذا كان مفتوحاً)
    updateModalContentIfVisible();
}

/**
 * يترجم كل العناصر التي تحمل data-i18n
 */
function translateGenericElements() {
    const elements = document.querySelectorAll('[data-i18n]');
    elements.forEach(el => {
        const key = el.getAttribute('data-i18n');
        const translatedText = t(key);
        if (translatedText) {
            if (el.tagName === 'INPUT' && el.getAttribute('placeholder')) {
                el.setAttribute('placeholder', translatedText);
            } else {
                el.textContent = translatedText;
            }
        }
    });
}

/**
 * يحدّث أزرار تبديل اللغة
 */
function updateLanguageButtons() {
    const langBtns = document.querySelectorAll('.lang-btn');
    langBtns.forEach(btn => {
        btn.classList.remove('active');
        const btnLang = btn.getAttribute('data-lang') || (btn.textContent.includes('English') ? 'en' : 'ar');
        if (currentLanguage === btnLang) {
            btn.classList.add('active');
        }
        // Ensure text is correct regardless of current state
        btn.textContent = btnLang === 'ar' ? 'العربية' : 'English';
        btn.setAttribute('data-lang', btnLang); // Ensure data-lang attribute is set
    });
}


// =================================================================
// 3. Page-Specific Update Functions (Safe-Coded)
// =================================================================

function updateNavigation() {
    const navKeys = {
        'dashboard': 'dashboard',
        'diets': 'diets',
        'dishes': 'dishes',
        'users': 'users',
        'notifications': 'notifications',
        'profile': 'users' // 'Profile' button is also labeled 'Users'
    };
    
    document.querySelectorAll('.nav-menu [data-page]').forEach(item => {
        const pageKey = item.getAttribute('data-page');
        const translationKey = navKeys[pageKey];
        if (translationKey) {
            const span = item.querySelector('span:last-child');
            if (span) {
                span.textContent = t(translationKey);
            }
        }
    });
    
    const logoText = document.querySelector('.logo-text');
    if (logoText) {
        logoText.textContent = t('logo');
    }
}

function updateGeneralUI() {
    const doctorCardText = document.querySelector('.doctor-card-text');
    if (doctorCardText) {
        doctorCardText.textContent = t('youAreHere');
    }

    const userName = document.querySelector('.user-name');
    if (userName) {
        userName.textContent = t('hiSamantha');
    }

    const backButtonText = document.querySelector('.back-btn span');
    if (backButtonText) {
        backButtonText.textContent = t('back');
    }
}

function updateUserTableAndForms() {
    // Users Table Headers
    const tableHeaders = document.querySelectorAll('.users-table th');
    const headerKeys = ['clientName', 'email', 'phone', 'dietPlan', 'adherence', 'actions'];
    tableHeaders.forEach((th, index) => {
        if (index < headerKeys.length) {
            th.textContent = t(headerKeys[index]);
        }
    });

    // User Form Labels
    document.querySelectorAll('.form-group label').forEach(label => {
        const htmlFor = label.getAttribute('for');
        let key = '';
        if (htmlFor === 'name') key = 'fullName';
        else if (htmlFor === 'email') key = 'email';
        else if (htmlFor === 'phone') key = 'phone';
        else if (htmlFor === 'address') key = 'address';
        else if (htmlFor === 'gender') key = 'gender';
        else if (htmlFor === 'dob') key = 'dob';
        
        if (key) {
            label.textContent = t(key);
        }
    });
    
    // Gender radio labels
    const maleLabel = document.querySelector('label[for="male"]');
    if (maleLabel) maleLabel.textContent = t('male');
    const femaleLabel = document.querySelector('label[for="female"]');
    if (femaleLabel) femaleLabel.textContent = t('female');
}

function updateDietsContent() {
    // Translate action buttons on diet cards
    document.querySelectorAll('.btn-delete, .diet-actions .delete-btn').forEach(btn => {
        const icon = btn.querySelector('i');
        btn.innerHTML = (icon ? icon.outerHTML + ' ' : '') + t('delete');
    });

    document.querySelectorAll('.btn-edit, .diet-actions .edit-btn').forEach(btn => {
        const icon = btn.querySelector('i');
        btn.innerHTML = (icon ? icon.outerHTML + ' ' : '') + t('edit');
    });

    document.querySelectorAll('.btn-add-info, .diet-actions .add-info-btn').forEach(btn => {
        const icon = btn.querySelector('i');
        btn.innerHTML = (icon ? icon.outerHTML + ' ' : '') + t('addInfo');
    });

    // Translate dish card titles
    document.querySelectorAll('.dish-card .dish-title').forEach(el => {
        const originalText = el.getAttribute('data-original-text') || el.textContent.trim();
        el.setAttribute('data-original-text', originalText);
        el.textContent = t(originalText);
    });
}

function updateRecentMealsContent() {
    // Translate action buttons
    document.querySelectorAll('.action-btn.check').forEach(el => {
        el.textContent = t('checkAdd');
    });
    document.querySelectorAll('.action-btn.false').forEach(el => {
        el.textContent = t('falseReject');
    });

    // Translate titles and descriptions
    document.querySelectorAll('.meal-item .meal-title-recent, .meal-item .meal-description, .meal-item .meal-components').forEach(el => {
        const originalText = el.getAttribute('data-original-text') || el.textContent.trim();
        el.setAttribute('data-original-text', originalText);
        el.textContent = t(originalText);
    });
}

function updateModalContentIfVisible() {
    const modal = document.getElementById("mealModal");
    if (modal && modal.style.display === "flex") {
        // Find the meal title in the modal
        const modalTitleEl = document.getElementById("modal-meal-title");
        if (!modalTitleEl) return;

        // Get the *English* title to find the meal data
        const originalTitle = modalTitleEl.getAttribute('data-original-text') || modalTitleEl.textContent.trim();
        
        // Find the meal data using the English title (or the AR translation if that's all we have)
        const meal = mealsData.find(m => m.title === originalTitle || t(m.title) === originalTitle);
        if (!meal) return;
        
        // Retranslate all modal fields
        modalTitleEl.textContent = t(meal.title);
        modalTitleEl.setAttribute('data-original-text', meal.title); // Store original English title
        
        document.getElementById("modal-meal-description").textContent = t(meal.description);

        const leftLabels = document.querySelectorAll('.modal-body .meal-details-left .detail-label');
        if (leftLabels.length >= 2) {
            leftLabels[0].textContent = t("description");
            leftLabels[1].textContent = t("nutritionalFacts");
        }
        
        const rightLabel = document.querySelector('.meal-details-right .detail-label');
        if (rightLabel) {
            rightLabel.textContent = t("components");
        }

        const saveBtn = document.getElementById('save-changes-btn');
        if (saveBtn) {
            saveBtn.textContent = t("saveChanges");
        }
    }
}


// =================================================================
// 4. Feature: Toast Message
// =================================================================

/**
 * تظهر رسالة توست مؤقتة
 * @param {string} messageKey - مفتاح الترجمة للرسالة
 */
function showToast(messageKey) {
    const toast = document.getElementById('toastMessage');
    if (toast) {
        const translatedMessage = t(messageKey); // ترجمة المفتاح
        toast.textContent = translatedMessage;
        toast.classList.add('show');

        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
}


// =================================================================
// 5. Feature: Meal Modal
// =================================================================

/**
 * تملأ وتظهر المودال بتفاصيل الوجبة
 * @param {string|number} mealId - ID الوجبة
 */
function showMealDetails(mealId) {
    const modal = document.getElementById("mealModal");
    const ingredientsList = document.getElementById("modal-ingredients-list");
    if (!modal || !ingredientsList) return;

    const meal = mealsData.find(m => m.id === parseInt(mealId));
    if (!meal) return;

    // Populate modal content with translated text
    const modalTitleEl = document.getElementById("modal-meal-title");
    modalTitleEl.textContent = t(meal.title);
    modalTitleEl.setAttribute('data-original-text', meal.title); // Store original English title
    
    document.getElementById("modal-meal-image").src = meal.image;
    document.getElementById("modal-meal-description").textContent = t(meal.description);
    document.getElementById("modal-meal-calories").textContent = `${meal.calories} Kcal`;
    document.getElementById("modal-meal-protein").textContent = `${meal.protein}g`;
    document.getElementById("modal-meal-carbs").textContent = `${meal.carbs}g`;
    document.getElementById("modal-meal-fat").textContent = `${meal.fat}g`;

    // Populate ingredients
    ingredientsList.innerHTML = '';
    meal.ingredients.forEach(ingredient => {
        const li = document.createElement('li');
        li.innerHTML = `
            <span>${ingredient.name}</span>
            <input type="checkbox" class="ingredient-checkbox" ${ingredient.checked ? 'checked' : ''}>
        `;
        ingredientsList.appendChild(li);
    });

    // Display the modal
    modal.style.display = "flex";

    // Re-apply language to modal labels
    updateModalContentIfVisible();
}


// =================================================================
// 6. Event Listener Setup
// =================================================================

/**
 * يربط جميع مستمعي الأحداث (Event Listeners)
 */
function setupEventListeners() {

    // --- Language Toggle ---
    document.querySelectorAll('.lang-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const lang = this.getAttribute('data-lang');
            setLanguage(lang);
        });
    });

    // --- Modal Listeners ---
    const modal = document.getElementById("mealModal");
    const closeBtn = document.querySelector(".close-btn");
    const dishesGrid = document.querySelector(".dishes-grid");
    const recentMealsList = document.querySelector(".recent-meals-list");
    const saveChangesBtn = document.getElementById("save-changes-btn");

    if (dishesGrid) {
        dishesGrid.addEventListener('click', (e) => {
            const dishCard = e.target.closest('.dish-card');
            if (dishCard) {
                showMealDetails(dishCard.dataset.mealId);
            }
        });
    }

    if (recentMealsList) {
        recentMealsList.addEventListener('click', (e) => {
            const mealItem = e.target.closest('.meal-item');
            if (!mealItem) return;

            const actionBtn = e.target.closest('.action-btn');

            if (!actionBtn) {
                // Clicked on meal body, show modal
                showMealDetails(mealItem.dataset.mealId);
                return;
            }

            // Handle Action Buttons
            const action = actionBtn.dataset.action;
            const mealId = mealItem.dataset.mealId;

            if (action === 'add') {
                showToast("addSuccessfully"); // Use key
                console.log(`Meal ID ${mealId} added.`);
                mealItem.remove();
            } else if (action === 'reject') {
                const confirmMessage = t("deleteConfirm");
                if (confirm(confirmMessage)) {
                    mealItem.remove();
                    showToast("dietDeleted"); // Use key
                    console.log(`Meal ID ${mealId} deleted.`);
                }
            }
        });
    }

    if (closeBtn) {
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }
    }

    if (modal) {
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    }

    if (saveChangesBtn) {
        saveChangesBtn.onclick = function() {
            alert(t("mealsAddedSuccess")); // Placeholder logic
            modal.style.display = "none";
        }
    }

    // --- Navigation & Other Button Listeners ---
    const recentMealsBtn = document.getElementById('recent-meals-btn');
    if (recentMealsBtn) {
        recentMealsBtn.onclick = () => {
            window.location.href = 'recent_meals.html'; // This should be updated in Laravel
        };
    }

    const backBtn = document.querySelector('.back-btn');
    if (backBtn) {
        backBtn.onclick = () => {
            window.location.href = 'dishes.html'; // This should be updated in Laravel
        };
    }

    // Header actions (placeholders)
    const notificationsBtn = document.getElementById('notifications-btn');
    if (notificationsBtn) {
        notificationsBtn.onclick = () => {
            alert("Navigating to Notifications. (Page not created yet)");
        };
    }

    const messagesBtn = document.getElementById('messages-btn');
    if (messagesBtn) {
        messagesBtn.onclick = () => {
            alert("Navigating to Messages. (Page not created yet)");
        };
    }
}


// =================================================================
// 7. App Initialization
// =================================================================

/**
 * دالة البداية الرئيسية للتطبيق
 */
function initializeApp() {
    setupEventListeners();
    setLanguage(currentLanguage); // Apply language on initial load
}

// Run the app once the DOM is ready
document.addEventListener('DOMContentLoaded', initializeApp);