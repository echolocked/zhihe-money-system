# Advanced Money System for Flarum

[![Latest Stable Version](https://poser.pugx.org/zhihe/money-system/v/stable)](https://packagist.org/packages/zhihe/money-system)
[![Total Downloads](https://poser.pugx.org/zhihe/money-system/downloads)](https://packagist.org/packages/zhihe/money-system)
[![License](https://poser.pugx.org/zhihe/money-system/license)](https://packagist.org/packages/zhihe/money-system)

Advanced money system for Flarum with content access control, view costs, and tag-based restrictions.

## Features

### 🎯 Core Functionality
- **View Costs**: Deduct money when users view posts/discussions
- **Zero-Money Blocking**: Prevent access when user money <= 0
- **Tag-Based Restrictions**: Require minimum money threshold for certain tags
- **Advanced Access Control**: Comprehensive permission system

### 💰 Money Management
- Built on top of `antoinefr/flarum-ext-money`
- **Initial Money**: Give new users starting money amount on registration
- Custom transaction logging
- Flexible cost configuration
- Admin controls for rates and restrictions

### 🔒 Access Control Levels
1. **Post Level**: Individual post view costs
2. **Discussion Level**: Discussion access requirements  
3. **Tag Level**: Minimum money requirements for tag access
4. **User Level**: Zero-money blocking

## Requirements

- Flarum 1.8.0+
- PHP 8.1+
- `antoinefr/flarum-ext-money` extension

## Installation

```bash
composer require zhihe/money-system
php flarum extension:enable zhihe-money-system
php flarum migrate
```

## Configuration

Access admin panel → Extensions → Zhihe Money System to configure:

- **Payment Amount**: Money deducted per discussion view
- **Initial Money**: Starting money amount for new users
- Tag-based money requirements (coming soon)
- Zero-money blocking settings
- Transaction logging options

## Usage

### For Administrators
- Set view costs for different content types
- Configure initial money amount for new users
- Configure tag-based restrictions
- Monitor user money transactions
- Manage access policies

### For Users
- Receive starting money upon registration
- View your money balance in profile
- Money is deducted when viewing discussions
- Earn money through participation (via antoinefr/flarum-ext-money)
- Track your spending history

## 🛠️ Development

This extension is designed to work seamlessly with the existing Flarum ecosystem while providing advanced money-based access control for content platforms.

### Local Development Setup

```bash
git clone https://github.com/echolocked/zhihe-money-system.git
cd zhihe-money-system
composer install
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

MIT License

## 🆘 Support

- **Issues**: [GitHub Issues](https://github.com/echolocked/zhihe-money-system/issues)
- **Community**: [Flarum Community Forum](https://discuss.flarum.org)

---

*Love this extension? Consider starring the repository and sharing it with the Flarum community! ⭐*