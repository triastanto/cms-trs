# Product Requirements Document

## Project Overview

**Project Name:** CMS-TRS (Content Management System)
**Type:** Laravel 12 + Filament 4 + Livewire CMS
**Tech Stack:** PHP 8.2, Laravel 12, Filament 4, Livewire, Flux, Vite, TailwindCSS 4
**Database:** SQLite (configurable to MySQL/PostgreSQL)
**Authentication:** Laravel Fortify with Two-Factor Authentication

## Core Features

### 1. Content Management

#### 1.1 Posts
- Full-featured blog post management with rich text editor
- **Fields:**
  - Title, slug (auto-generated, editable)
  - Content (rich editor with: bold, italic, underline, strike, headers, lists, blockquote, code blocks, links, file attachments)
  - Excerpt (optional, auto-generated from content if empty)
  - Status: draft, published, archived
  - Published date/time
  - Author (user relationship)
  - Primary category (single)
  - Multiple categories (many-to-many)
  - Tags (many-to-many)
  - Featured image with image editor (aspect ratios: 16:9, 4:3, 1:1)
  - Gallery images (multiple, reorderable)
  - SEO: meta title (60 chars), meta description (160 chars)
- **Media Handling:**
  - Spatie Media Library integration
  - Featured image conversions: thumb (368x232), preview (800x600)
  - Gallery image conversions: thumb (368x232)
  - Accepted formats: JPEG, PNG, GIF, WebP
  - Max size: 2048KB
- **Post Scopes:** published, draft
- **Route Key:** slug

#### 1.2 Categories
- Hierarchical category system with parent-child relationships
- **Fields:**
  - Name, slug (auto-generated, editable)
  - Description
  - Color
  - Parent category
  - Sort order
  - Active/inactive status
  - Thumbnail image with conversion (200x200)
- **Features:**
  - Unlimited nesting levels
  - Full path display (breadcrumb-style)
  - Sorting capability
  - Active/root scopes
- **Route Key:** slug

#### 1.3 Tags
- Simple tag taxonomy
- **Fields:**
  - Name, slug (auto-generated, editable)
  - Description
  - Color
  - Active/inactive status
- **Features:**
  - Many-to-many relationship with posts
  - Active scope filtering
- **Route Key:** slug

### 2. Navigation Management

#### 2.1 Menus
- **Fields:**
  - Name
  - Location (unique identifier)
  - Description
  - Active/inactive status
- **Predefined Locations:**
  - Header Primary
  - Header Secondary
  - Footer Primary
  - Footer Secondary
  - Sidebar
  - Mobile
- **Features:**
  - Location-based menu retrieval
  - Active menu filtering

#### 2.2 Menu Items
- Hierarchical menu structure with unlimited nesting
- **Fields:**
  - Menu ID (parent menu)
  - Parent ID (parent menu item for nesting)
  - Title
  - URL
  - Target: _self, _blank, _parent, _top
  - Icon
  - CSS classes
  - Sort order
  - Active/inactive status
  - External link flag
- **Features:**
  - Recursive children relationships
  - Auto-protocol handling for external links
  - Active/root scopes

### 3. Media Management

#### 3.1 Media Library
- Centralized media management using Spatie Media Library
- **Storage:**
  - Disk: public
  - Collections: featured_image, gallery, thumbnail
- **Image Processing:**
  - Automatic thumbnail generation
  - Multiple size conversions
  - Sharpening filter (level 10)

### 4. User Management & Authentication

#### 4.1 Users
- **Fields:**
  - Name, email, password
  - Email verification
  - Two-factor authentication fields
- **Features:**
  - User initials generation
  - Post authorship tracking
  - Filament panel access control

#### 4.2 Authentication System
- **Routes:**
  - Login, Register
  - Password reset flow
  - Email verification
  - Two-factor authentication setup
  - Logout
- **Features:**
  - Laravel Fortify integration
  - Password confirmation for sensitive actions
  - Throttling on verification routes (6 attempts per minute)

#### 4.3 User Settings
- Profile management
- Password change
- Appearance settings
- Two-factor authentication management

### 5. Admin Panel (Filament)

#### 5.1 Navigation Structure
- **Content Management Group:**
  - Posts (priority 1)
  - Categories
  - Tags
  - Media
  - Menus
  - Menu Items

#### 5.2 Resource Features
- CRUD operations for all entities
- List views with filtering and search
- Form validation
- Relationship management
- Collapsible form sections
- Real-time slug generation
- Live search in selects
- Image uploads with editor

### 6. Frontend

#### 6.1 Public Routes
- Homepage (welcome page)
- Dashboard (authenticated users)
- Settings routes (profile, password, appearance, 2FA)
- Authentication routes

#### 6.2 Livewire Components
- Authentication components (Login, Register, Password Reset, Email Verification)
- Settings components (Profile, Password, Appearance, TwoFactor)
- Blog components (structure exists)
- Homepage components (structure exists)

### 7. Development & Testing

#### 7.1 Development Tools
- Laravel Pint (code formatting)
- PHPStan/Larastan (static analysis)
- Laravel Pail (log monitoring)
- Pest (testing framework)
- Paratest (parallel testing)

#### 7.2 Testing Structure
- Unit tests
- Feature tests
- Browser tests (fixtures available)
- Test case base class

#### 7.3 Development Scripts
- `composer dev`: Concurrent server, queue, and Vite
- `composer test`: Config clear + test run
- Vite for asset bundling

### 8. Database Schema

#### 8.1 Tables
- **users:** id, name, email, password, timestamps, 2FA fields
- **posts:** id, title, slug (unique), content, excerpt, status (enum), meta_title, meta_description, published_at, user_id, category_id, timestamps
- **categories:** id, name, slug, description, color, parent_id, sort_order, is_active, timestamps
- **tags:** id, name, slug, description, color, is_active, timestamps
- **post_tag:** post_id, tag_id (pivot)
- **category_post:** category_id, post_id (pivot)
- **menus:** id, name, location (unique), description, is_active, timestamps
- **menu_items:** id, menu_id, parent_id, title, url, target, icon, css_class, sort_order, is_active, is_external, timestamps
- **media:** Spatie Media Library schema
- **cache:** id, key, value, expiration
- **jobs:** queue system tables
- **sessions:** session storage

#### 8.2 Indexes
- posts: (status, published_at), slug
- Other unique constraints and foreign keys as defined

### 9. Configuration

#### 9.1 Environment Variables
- App configuration (name, env, debug, URL, locale)
- Database (SQLite default, MySQL/PostgreSQL support)
- Session (database driver)
- Queue (database connection)
- Cache (database store)
- Mail (log driver for development)
- File storage (local disk default)
- Broadcasting (log connection)

#### 9.2 Asset Build
- Vite 7 configuration
- TailwindCSS 4.1
- Autoprefixer
- Laravel Vite plugin
- Concurrently for parallel processes

## Technical Implementation

### Models & Relationships
- Post → User (belongsTo)
- Post → Category (belongsTo, primary)
- Post → Categories (belongsToMany, additional)
- Post → Tags (belongsToMany)
- Category → Parent (belongsTo)
- Category → Children (hasMany)
- Category → Posts (belongsToMany)
- Tag → Posts (belongsToMany)
- Menu → MenuItems (hasMany)
- MenuItem → Menu (belongsTo)
- MenuItem → Parent (belongsTo)
- MenuItem → Children (hasMany)
- User → Posts (hasMany)

### Traits & Interfaces
- HasFactory (all models)
- InteractsWithMedia (Post, Category)
- HasMedia interface (Post, Category)
- TwoFactorAuthenticatable (User)
- FilamentUser interface (User)

### Scopes
- Post: published, draft
- Category: active, root
- MenuItem: active, root
- Tag: active
- Menu: active

### Accessors & Mutators
- Post: excerpt (auto-generate from content)
- Category: full_path (breadcrumb generation)
- MenuItem: full_url (protocol handling)
- User: initials

### Form Validation
- Unique slug validation (per model type)
- Alpha-dash for slugs
- Required fields enforcement
- Max length constraints (title: 255, meta_title: 60, meta_description: 160)
- Image file type and size validation

### Media Conversions
- Featured image: thumb (368x232), preview (800x600)
- Gallery: thumb (368x232)
- Category thumbnail: thumb (200x200)
- Sharpening: level 10

### Auto-Generation Features
- Slugs from titles/names (on create, optional on update)
- Post excerpts from content (if empty)
- Default status: draft (posts)
- Default active: true (menus, categories, tags, menu items)
- Default author: current authenticated user

### Security Features
- Password hashing (bcrypt, configurable rounds)
- Two-factor authentication
- Email verification
- Password confirmation for sensitive actions
- Route throttling
- CSRF protection
- Signed routes for email verification

### Code Quality
- PSR-4 autoloading
- Laravel Pint formatting
- PHPStan level 3 static analysis
- Pest testing framework
- Strict typing where applicable
