# Web-Based Quiz Application (Pure PHP) — Implementation Plan (QA → UI → Backend)

> Tech: **Pure PHP (no framework)**, MySQL, HTML5, CSS3, **Bootstrap 5**, jQuery  
> Goals: **Bangla support**, **mobile-first responsive UI**, clean code, security best practices, scalable structure.

---

## 0) Scope Summary (from client doc)
- User: signup/login with **mobile + password (bcrypt)**, unique mobile
- Credits: signup **100 TK bonus**, purchase credits (**1 TK = 1 credit**, min 50), spend credits to play quiz
- Referral: each user has referral URL; referrer gets **50 TK referral credit** only when:
  1) referred user uses their **100 TK bonus**
  2) then purchases credits
- Quiz: Bangla, monthly cycle reset, points for correct, leaderboard top 5, last month winner
- Wallet logs: full transaction ledger (bonus/purchase/referral/quiz/withdraw)
- Withdraw: referral credits withdrawable via **Bkash/Nogod** (not quiz winnings)
- Admin: manage users, questions, transactions, withdrawals, monthly top scorers, reset automation
- UI tabs: Quiz Play, My Account, Referral, Winner (+ Auth pages)

---

## 1) Milestones Delivery Strategy
### Milestone 1 — Project QA ($5)
Deliver:
- Final page list + navigation
- DB schema draft (tables + fields)
- Wireframe + **UI prototype (3 pages)**: Login/Signup, Dashboard, Quiz Play (responsive)
- Notes on referral condition + monthly reset logic

### Milestone 2 — HTML Design ($5)
Deliver:
- Full responsive UI (all required pages)
- Consistent components (navbar/tabs/cards/buttons/forms)
- Bangla-friendly typography
- Sample/dummy data views
- Admin UI base layouts

### Milestone 3–5 — Backend (PHP/MySQL), Payments, Final delivery
Proceed after UI approval.

---

## 2) Folder Structure (Pure PHP, MVC-ish but light)
