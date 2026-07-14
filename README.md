# Premium International Athlete Portfolio — Muhammad Husnain ul Haq Malik

A world-class, premium, luxury personal athlete portfolio website designed and developed from scratch for **Muhammad Husnain ul Haq Malik** (Pakistan Soft Tennis Player, Regional Cricketer, and Computer Science Student at Adamjee Government Science College).

Inspired by the design aesthetics of premium brands like Nike, Wimbledon, Rolex, and Apple, this site features deep emerald greens, royal golds, custom smooth animations, a glassmorphic user interface, and highly interactive components.

---

## 🚀 Features

### 🌟 Design & UI/UX
- **Luxury Theme**: Sports-focused design featuring deep blacks (`#0B0B0B`), charcoal grays, emerald green highlights (`#00C853`), and royal gold accents (`#FFD700`).
- **Glassmorphism**: Translucent panels with blur backdrops and animated glowing borders.
- **Ambient Particles Background**: An interactive HTML5 2D Canvas particle simulation rendering green and gold nodes that connect dynamically.
- **Custom Lag Cursor**: A royal gold pointer cursor with an emerald green trailing ring tracking mouse momentum.
- **Interactive Mouse-Glow Cards**: Cards that calculate mouse position on hover to render dynamic radial lighting gradients.
- **Micro-Animations**: Smooth keyframe movements, page loading screen, ripple-like luxury buttons, and hover tilt effects.

### ⚽ Career & Academic Showcase
- **Home**: Large hero section showcasing titles, typing roles animation, quick CTAs, and a floating scroll guide.
- **About**: Custom tab panels highlighting biography, mission, vision, and core details.
- **Education**: Science college milestone timeline.
- **Sports Career**: Detailed summaries of Soft Tennis court training and regional cricket representation.
- **Career Timeline**: Alternating vertical milestones mapping athletic accomplishments.
- **Statistics Counter**: Animated number count-up triggered automatically as they enter the viewport.
- **Achievements & Skills**: Circular skills indicators for court leadership, and sliding bars for technical capabilities.
- **Training Routine**: Tabbed schedule tables detailing weekly activities.
- **Masonry Gallery & Lightbox**: Dynamic filters separating Tennis, Cricket, and Certificates with a premium keyboard-accessible media Lightbox modal.

### 🛡️ Backend Contact Form
- Fully responsive contact section featuring AJAX form handling.
- **PHP Secure contact.php**: Server-side sanitization, honeypot anti-spam filters, and responsive JSON data feedback.

---

## 📁 File Structure

```text
portfolio/
├── index.html          # Core semantic page structure, SEO meta, schema JSON-LD
├── css/
│   ├── variables.css   # Color variables, fonts, resets, scrollbar
│   ├── style.css       # Layout styles, headers, footers, component styles
│   ├── responsive.css  # Mobile and tablet responsiveness overrides
│   └── animations.css  # Reveal states, floatings, glows, and morphes
├── js/
│   ├── app.js          # Preloader, nav scrollspy, hover glows, sliders, accordion
│   ├── cursor.js       # Custom cursor tracking logic
│   ├── particles.js    # Canvas particle nodes
│   ├── typing.js       # Hero section typing titles
│   ├── counter.js      # Animated stats counters
│   └── gallery.js      # Masonry filters and lightbox overlays
└── php/
    └── contact.php     # PHP AJAX mailer & input validator
```

---

## 🛠️ Setup & Local Testing

### Option A: Static Frontend Only
You can double-click or open `index.html` directly in any web browser. The entire frontend, animations, canvas particles, filters, and lightboxes will function immediately.

### Option B: PHP contact form (XAMPP / local server)
1. Place this project folder (`hunain/`) inside XAMPP's root: `C:\xampp\htdocs\hunain\`.
2. Start Apache via your XAMPP Control Panel.
3. Open your browser and navigate to `http://localhost/hunain/`.
4. Submit the contact form to trigger the AJAX server-side mailer simulation.

---

## ⚙️ Optimization & Accessibility
- **Lighthouse Performance**: Built with pure Vanilla HTML/CSS/JS without bulky third-party libraries, ensuring rapid load times and zero layout shifts (CLS).
- **SEO Ready**: Incorporates Schema.org person markup, Open Graph social tags, and semantic structures.
- **Accessibility**: Includes keyboard listeners for modal navigation (`Escape`, `ArrowLeft`, `ArrowRight`) and appropriate HTML element titles.
