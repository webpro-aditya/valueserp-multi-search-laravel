# ValueSERP Multi Search (Laravel) – With CSV Export

---

## 🚀 Features:

- Search multiple queries using **ValueSERP API**.  
- **AJAX-based Search** – No page reloads.  
- Full data export as **CSV** on button click.  
- Loading **spinner & backdrop** while processing.  
- Clean & user-friendly interface.  

---

## ⚙️ Project Setup:

### 1. Clone Repo:
```bash
git clone https://github.com/webpro-aditya/valueserp-multi-search-laravel.git
cd valueserp-multi-search-laravel
```

### 2. Install Dependencies:
```bash
composer install
```

### 3. Create `.env`:
```bash
cp .env.example .env
```

Edit `.env` and set:
```
VALUESRP_API_URL=https://api.valueserp.com/search  
VALUESRP_API_KEY=your_real_or_test_key_here  
```

Also update in `config/services.php` (if required):
```php
'apikeys' => [
    'valuesrp_api_url' => env('VALUESRP_API_URL'),
    'valuesrp_api_key' => env('VALUESRP_API_KEY'),
],
```

### 4. Generate Key & Migrate:
```bash
php artisan key:generate
php artisan migrate
```

### 5. Serve the App:
```bash
php artisan serve
```

Visit: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 🧪 Test Functionality:

- Enter multiple keywords (e.g., `"Laravel"`, `"PHP"`, `"React"`) in the search fields.  
- Click **Search** → AJAX loads the data using **ValueSERP API**.  
- **Export All Data (CSV)** button downloads the full result set as CSV.  
- Loading **spinner & backdrop** visible while request is being processed.  

---

## ⚠️ Note:

- Replace `your_real_or_test_key_here` in `.env` with an actual [ValueSERP API key](https://valueserp.com/) (**FREE trial available**).  
- Tested using **Laravel 12.15.0** and **PHP 8.2.0** and **Composer 2.6.5**.  

---
