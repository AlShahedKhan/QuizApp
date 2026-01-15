# Homepage UI & UX Plan — Web-Based Quiz Application

## Goal
Create a **modern, attractive, mobile-first homepage** that:
- Clearly explains the quiz platform
- Highlights offers, rewards, and referral benefits
- Converts visitors into registered users
- Works perfectly in Bangla-first, English-optional mode

Tech: HTML5, CSS3, Bootstrap 5, jQuery  
No framework, clean semantic HTML

---

## 1. Homepage Sections (Top → Bottom)

### 1.1 Header / Navbar
- Logo (text-based if no image provided)
- Navigation:
  - Home
  - How It Works
  - Packages
  - Winners
  - Login
  - Signup (Primary CTA)
- Sticky on scroll
- Mobile: hamburger menu

Best Practice:
- Use `<nav>` semantic tag
- Clear CTA button for Signup

---

### 1.2 Hero Section (Above the Fold)
**Purpose:** Instantly explain value

Content:
- Big headline (Bangla):
  > খেলুন কুইজ, জিতুন আকর্ষণীয় পুরস্কার
- Sub text:
  > প্রতি মাসে কুইজ খেলুন, পয়েন্ট অর্জন করুন এবং জিতে নিন ক্যাশ রিওয়ার্ড
- CTA Buttons:
  - “এখনই শুরু করুন” (Signup)
  - “কিভাবে কাজ করে” (scroll)

UI:
- Gradient background
- Card-based layout
- Large readable fonts
- Mobile-first padding

---

### 1.3 Key Highlights / Features
Display in **3–4 cards**

Cards:
- 🎁 Signup Bonus  
  “রেজিস্ট্রেশন করলেই ১০০ টাকা বোনাস”
- 🧠 Monthly Quiz  
  “প্রতি মাসে নতুন কুইজ ও নতুন লিডারবোর্ড”
- 💰 Cash Rewards  
  “সর্বোচ্চ ৩০,০০০ টাকা পর্যন্ত পুরস্কার”
- 🤝 Referral Income  
  “বন্ধু আনুন, ৫০ টাকা রিওয়ার্ড পান”

Best Practice:
- Equal height cards
- Icons using Bootstrap Icons / Font Awesome

---

### 1.4 How It Works (Step Section)
4 simple steps (horizontal on desktop, vertical on mobile):

1. Signup করুন  
2. বোনাস ও ক্রেডিট ব্যবহার করে কুইজ খেলুন  
3. পয়েন্ট অর্জন করুন  
4. জিতে নিন পুরস্কার / রেফারেল আয়

Use:
- Numbered circles
- Light background contrast

---

### 1.5 Offers & Packages (Important)
**Credit Purchase Packages**

Display as pricing cards:

#### Example Packages
- Starter Pack  
  - 50 Credits = 50 TK  
  - Best for beginners

- Popular Pack (Highlight)  
  - 200 Credits = 200 TK  
  - Most played users

- Pro Pack  
  - 500 Credits = 500 TK  
  - Maximum quiz access

Notes:
- “1 TK = 1 Credit” clearly shown
- “Credits are used per question”
- CTA: “Buy Credits” → Login required

Best Practice:
- Highlight middle package
- Use badge: “Popular”

---

### 1.6 Referral Offer Section
Explain referral income clearly:

Text:
> আপনার রেফারেল ব্যবহার করে কেউ বোনাস শেষ করে ক্রেডিট কিনলে  
> আপনি পাবেন **৫০ টাকা রেফারেল রিওয়ার্ড**

Include:
- Simple flow diagram (text-based)
- Withdraw methods:
  - bKash
  - Nogod

CTA:
- “রেফারেল লিংক তৈরি করুন” → Signup/Login

---

### 1.7 Winner Showcase
- Last month’s winner
- Score
- Prize amount
- Month name

UI:
- Card with trophy icon
- Trust-building section

---

### 1.8 Call to Action (Final CTA)
Big section with contrast background:

Text:
> আজই শুরু করুন, আপনার জয়ের গল্প লিখুন

Buttons:
- Signup Now
- Login

---

### 1.9 Footer
- Links:
  - About
  - Terms & Conditions
  - Privacy Policy
  - Contact
- Copyright
- Simple & clean

---

## 2. Homepage Best Practices
- Semantic HTML (`header`, `section`, `footer`)
- Mobile-first CSS
- No inline CSS
- Reusable utility classes
- Bangla UTF-8 safe font
- Lazy load heavy assets (future)
- CTA visible without scroll

---

## 3. Required Homepage Files
views/
└── home/
└── index.php (HTML only initially)

assets/
├── css/home.css
└── js/home.js


---

## 4. Extra Pages Required (Add to Project Plan)

These pages are recommended for clarity & trust:

1. **How It Works Page**
   - Detailed explanation
   - Linked from homepage

2. **Packages Page**
   - Full credit packages
   - Pricing clarity

3. **Winners Archive Page**
   - Previous months winners
   - Builds credibility

4. **Terms & Conditions**
   - Required for payments & withdrawals

5. **Privacy Policy**
   - Data & mobile number usage

(Add these after Homepage + Core App UI)

---

## 5. Milestone Mapping Update
- Milestone 1 (QA):
  - Homepage wireframe + section plan
- Milestone 2 (HTML Design):
  - Full Homepage UI
  - Responsive + polished
- Later milestones:
  - Dynamic data via PHP

---

## 6. Acceptance Criteria (Homepage)
- Looks professional on mobile & desktop
- All CTAs visible and clear
- Bangla text readable
- No broken layout
- Ready for backend integration

---
End of homepage.md
