---
paths:
  - 'app/Http/Controllers/**'
---

# Controllers

## Single-action controllers
New endpoints get a single-action `__invoke` controller with a short docblock saying why the endpoint exists. Pages themselves are drawn by the CMS's routes; the website adds endpoints only for what is its own.

## Controllers are plain classes
Don't extend the base `Controller`; write controllers as plain classes (extend a vendor controller only to override it).
