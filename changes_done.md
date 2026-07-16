# LMS Updates & Bug Fixes Summary (July 2026)

Bhai, maine saare requirements ke mutabiq changes kar diye hain. Ye summary file aapke reference ke liye hai ke kya kya kaam hua hai:

---

## 1. Passing Threshold Fix (50% Rule)
* **Database Updates:**
  - `exams` table mein default `pass_threshold` ko `40` se badal kar `50` kar diya gaya hai.
  - Live database mein query run karke saare exams ki threshold ko `50%` kar diya gaya hai.
  - `database_schema.sql` file ko bhi update kar diya gaya hai taake future import mein direct `50` threshold aaye.
* **Evaluation Engine & Dashboard (Backend):**
  - [DashboardService.php](file:///d:/LMS/app/Services/DashboardService.php) mein evaluation logic ko change kiya hai. Ab student ka score agar `>= 50.00%` hoga toh hi pass mana jayega. Agar `< 50.00%` hoga toh fail lockout trigger hoga aur student ko £229 resit fee pay karni hogi reactivate karne ke liye.
* **WhatsApp Failure Alert:**
  - Fail hone par jo automated WhatsApp alert send hota tha, uski text ko update kar ke ab `50% proficiency threshold` kar diya gaya hai.
* **Dashboard Indicators (Frontend):**
  - [dashboard.blade.php](file:///d:/LMS/resources/views/lms/dashboard.blade.php) mein jitne bhi 40% ke references thay (exam score badge, status flags, rules summary, and history logs) un sabko update kar ke `50%` kar diya gaya hai.

---

## 2. 14-Day Lock Corrected
* **Phase I** - Academic English, Advanced Mathematics, Essential Sciences are open from Day 1 with no restrictions.
* **Phase II** - Specialty modules are locked for 14 days. Access is granted automatically on Day 15 or manually via Admin "Expedite" button.
* **Database & Code Changes:**
  - `modules` table mein `phase` column (VARCHAR(10) DEFAULT 'I') add kiya gaya hai. Modules 1, 2, aur 3 ko `phase = 'I'` aur Module 4 ko `phase = 'II'` assign kiya hai.
  - Backend (`DashboardService.php`) aur frontend (`dashboard.blade.php`) dono mein lock conditional logic ko update kiya gaya hai ke sirf **Phase II modules** (`phase = 'II'`) aur final assessment lock rahein.

---

## 3. Homepage Text & Input Placeholders
* [home.blade.php](file:///d:/LMS/resources/views/lms/home.blade.php) mein exact requirements ke mutabiq 4 lines aur placeholders add kiye gaye hain:
  - **Passing Threshold:** *"Passing Threshold: Assessment validation is configured at 50%. A score below 50% constitutes a fail, triggering account lockout."*
  - **Installments:** *"Program Fees Schedule: Complete tuition clearance is fixed at £2,249 full price or cleared in three sequential installments starting with £751 followed by two payments of £749 each."*
  - **Footnote:** Prospectus Page 4 ke bilkul niche ye footnote add kiya gaya hai: `*Optional Partner University Placement Concierge package available post-graduation for £249.`
  - **Serial ID Placeholder:** Label aur input field placeholder ko update kar ke `REG-LDN-2026-00001 or CTR-LDN-2026-00001` kiya gaya hai (purana `REG-LON` typo correct kiya gaya hai).

---

## Files Modified:
1. [database_schema.sql](file:///d:/LMS/database_schema.sql) (Database structures and seeds)
2. [DashboardService.php](file:///d:/LMS/app/Services/DashboardService.php) (Backend evaluation & WhatsApp alert logic)
3. [dashboard.blade.php](file:///d:/LMS/resources/views/lms/dashboard.blade.php) (Frontend dashboard views, pass tags, lock overrides)
4. [home.blade.php](file:///d:/LMS/resources/views/lms/home.blade.php) (Homepage exact lines, serial placeholders)
