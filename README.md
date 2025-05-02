# Champions League Simulation

A Laravel-based football championship simulation system that realistically simulates matches and predicts championship chances based on team strengths.

## 🎯 System Requirements

### Technical Requirements
- PHP >= 8.2
- Node.js >= 18.x
- Composer >= 2.0

### PHP Extensions
- BCMath PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

## 🚀 Getting Started

### Installation
1. Clone the repository
```bash
git clone [repository-url]
```

2. Install PHP dependencies
```bash
composer install
```

3. Install JavaScript dependencies
```bash
npm install
```

4. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

5. Run migrations
```bash
php artisan migrate:fresh --seed
```

6. Start the development server
```bash
composer run dev
```

## 🏆 Features

### Team Management
- Teams with customizable attributes:
  - Name
  - Strength (0-100)
  - Supporter Strength(0-100)
  - Logo
- Balanced fixture generation ensuring equal home/away games
- Support for multiple teams from different countries

### Match Simulation
- Realistic score generation based on:
  - Team power ratings (0-100 scale)
  - Home advantage (up to 15% boost) based on supporter strength
  - Away team boost (up to 3% boost) based on supporter strength
  - Weather conditions affecting goal scoring:
    - Sunny (60% chance): 2.5x goal factor
    - Rainy (25% chance): 2.0x goal factor
    - Very Rainy (10% chance): 1.5x goal factor
    - Snowy (5% chance): 1.0x goal factor
  - Form factor (up to 10% boost/reduction) based on recent performance
  - Fatigue factor (random 15% reduction) affecting team performance
  - Power-based reduction for weaker teams:
    - Teams < 65 power: 40% reduction
    - Teams < 80 power: 20% reduction
    - Teams >= 80 power: No reduction
- Advanced scoring algorithm:
  - Poisson distribution for goal sampling
  - Minimum expected goals: 0.1
  - Dynamic goal factors based on team power and conditions
  - No maximum score cap, but rare due to Poisson distribution

### Championship Management
- Weekly match simulations
- Automatic standings updates:
  - Points (3 for win, 1 for draw)
  - Goals for/against
  - Goal difference
  - Games played/won/drawn/lost
- Championship predictions considering:
  - Current points
  - Remaining matches
  - Team strength
  - Recent form
  - Head-to-head records

## 🛠️ Technical Stack

### Backend
- Laravel 12.x
- Inertia.js
- MySQL Database
- Laravel Sanctum for Authentication

### Frontend
- Vue 3 with TypeScript
- Tailwind CSS
- Inertia.js for seamless integration
- Reka UI components

### Development Tools
- Vite for asset bundling
- ESLint for code linting
- Prettier for code formatting
- PHPUnit for testing

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

### Test Coverage
- Fixture generation
- Match simulation
- Standings calculation
- Team strength impact
- Score distribution
- Championship predictions

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request 