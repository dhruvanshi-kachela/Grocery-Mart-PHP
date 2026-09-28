# Product Requirements Document (PRD)

# Grocery Mart - Online Grocery Shopping Website

**Technology Stack:** PHP, MySQL, HTML5, CSS3, JavaScript, Bootstrap 5  
**Project Type:** Final Year University Project  
**Version:** 1.0

# 1\. Project Overview

## Product Name

**Grocery Mart**

## Product Description

Grocery Mart is a full-stack web application that allows customers to browse grocery products, add items to their shopping cart, place online orders, manage addresses, and track order status. The platform also provides an Admin Panel to manage products, categories, customers, orders, inventory, coupons, and reports.

The objective of this project is to simulate a real-world online grocery shopping platform similar to Blinkit, BigBasket, Zepto, and Instamart while remaining achievable as a university project.

# 2\. Problem Statement

Traditional grocery shopping requires customers to physically visit stores, leading to time consumption and inconvenience.

This website solves that problem by providing:

- Online grocery shopping
- Secure authentication
- Cart management
- Online ordering
- Order tracking
- Admin inventory management
- Discount coupons
- Customer order history

# 3\. Goals

### Customer Goals

- Easy registration
- Browse groceries
- Search products
- Add to cart
- Secure checkout
- Order history
- Profile management

### Admin Goals

- Manage products
- Manage inventory
- Manage orders
- Manage users
- Generate reports
- Monitor sales

# 4\. User Roles

## Customer

Permissions

- Register
- Login
- Browse products
- Add wishlist
- Add cart
- Place orders
- Track orders
- Review products
- Manage addresses
- Update profile

## Admin

Permissions

- Dashboard
- Category management
- Product management
- Inventory
- Orders
- Coupons
- Reports
- User management
- Reviews management
- Settings

# 5\. Technology Stack

Backend

- PHP 8+
- Core PHP (MVC Structure)

Frontend

- HTML5
- CSS3
- Bootstrap 5
- JavaScript
- AJAX
- jQuery

Database

- MySQL

Development Environment

- XAMPP

Version Control

- Git

IDE

- VS Code

# 6\. Website Pages

## Public Pages

### Home

Contains

- Hero Banner
- Featured Categories
- Popular Products
- Today's Offers
- Best Sellers
- Why Choose Us
- Customer Reviews
- Newsletter
- Footer

### About Us

- Company introduction
- Mission
- Vision

### Shop

- Product Listing
- Filters
- Categories
- Search
- Sorting

### Product Details

Contains

- Images
- Product Name
- Price
- Discount
- Description
- Stock
- Rating
- Reviews
- Quantity Selector
- Add to Cart
- Add Wishlist

### Categories

Display all grocery categories.

Example

- Fruits
- Vegetables
- Dairy
- Bakery
- Snacks
- Drinks
- Frozen Food
- Household
- Personal Care

### Contact

Contains

- Contact Form
- Email
- Phone
- Google Map

### FAQ

Frequently Asked Questions

### Privacy Policy

### Terms & Conditions

# Authentication Pages

## Register

Fields

- Full Name
- Email
- Mobile
- Password
- Confirm Password

Validation

- Email unique
- Password length
- Mobile validation

## Login

Fields

- Email
- Password

Features

- Remember Me
- Forgot Password

## Forgot Password

Flow

Email

↓

Verification

↓

Reset Password

# Customer Dashboard

Sidebar

Dashboard

Orders

Wishlist

Cart

Addresses

Reviews

Profile

Logout

## Dashboard

Shows

- Total Orders
- Pending Orders
- Delivered Orders
- Wishlist Count

## Profile

Update

- Name
- Mobile
- Password
- Profile Picture

## Address Book

CRUD Operations

- Add Address
- Edit Address
- Delete Address

## Wishlist

- Add
- Remove
- Move to Cart

## Shopping Cart

Features

- Quantity Update
- Remove Item
- Coupon Apply
- Price Summary

## Checkout

Sections

Shipping Address

Payment Method

Order Summary

Coupon

Place Order

## Payment

For university project

- Cash on Delivery
- Dummy Card Payment (simulation only)

## Order Success

Displays

- Order Number
- Invoice Download
- Continue Shopping

## Order History

Contains

- Order Date
- Status
- Items
- Invoice
- Cancel Order

## Order Tracking

Status

Order Placed

↓

Packed

↓

Shipped

↓

Out for Delivery

↓

Delivered

# Admin Panel

## Login

Separate Admin Login

## Dashboard

Cards

Total Orders

Revenue

Customers

Products

Pending Orders

Low Stock

Charts

Monthly Sales

Category Sales

Recent Orders

## Category Management

CRUD

Category Image

Status

## Product Management

CRUD

Fields

Name

Description

Category

Price

Discount

Stock

Images

Brand

Unit

Weight

Status

## Inventory

Stock Update

Low Stock Alert

Out of Stock

## Order Management

View Orders

Update Status

Generate Invoice

Cancel Order

## Customer Management

View Customers

Search

Block

Delete

## Coupon Management

CRUD

Coupon Code

Discount

Expiry Date

Minimum Purchase

## Review Management

Approve

Reject

Delete

## Contact Queries

View

Delete

Reply

## Reports

Sales Report

Customer Report

Inventory Report

Product Report

Revenue Report

# Navigation Bar

Logo

Search

Categories

Wishlist

Cart

Profile

Login

Register

# Footer

Quick Links

Categories

Contact

Social Media

Newsletter

Copyright

# Product Categories

Fresh Fruits

Fresh Vegetables

Dairy

Bakery

Beverages

Snacks

Rice

Flour

Oil

Spices

Frozen Food

Cleaning Supplies

Beauty Products

Baby Care

Pet Food

# Search Features

Keyword Search

Category Filter

Price Filter

Sorting

Newest

Popularity

Price Low to High

Price High to Low

Rating

# Functional Requirements

## Authentication

Register

Login

Logout

Session Management

Password Hashing

Forgot Password

Role Based Login

## Product

Browse

Search

Filter

Sort

Pagination

Wishlist

Reviews

Ratings

## Cart

Add

Remove

Update Quantity

Subtotal

Coupon

Tax

Grand Total

## Checkout

Address Selection

Payment

Order Summary

Invoice

## Admin

CRUD Products

CRUD Categories

CRUD Coupons

Manage Inventory

Manage Orders

Reports

# Non Functional Requirements

Responsive Website

Fast Loading

Secure Login

SQL Injection Prevention

Cross Browser Support

Input Validation

Error Handling

Maintainable Code

Scalable Folder Structure

# Database Design

## Tables

### users

id

name

email

mobile

password

role

profile_image

created_at

### admins

id

name

email

password

### categories

id

name

image

status

### products

id

category_id

name

description

price

discount

stock

brand

unit

weight

image

status

### product_images

id

product_id

image

### cart

id

user_id

product_id

quantity

### wishlist

id

user_id

product_id

### addresses

id

user_id

full_address

city

state

country

zipcode

### orders

id

user_id

address_id

total

discount

payment_method

payment_status

order_status

created_at

### order_items

id

order_id

product_id

quantity

price

### coupons

id

code

discount

minimum_amount

expiry_date

status

### reviews

id

user_id

product_id

rating

review

created_at

### contact_messages

id

name

email

message

# Folder Structure

grocery-mart/  
<br/>admin/  
<br/>assets/  
<br/>css/  
<br/>js/  
<br/>images/  
<br/>uploads/  
<br/>config/  
<br/>controllers/  
<br/>models/  
<br/>views/  
<br/>includes/  
<br/>database/  
<br/>sql/  
<br/>user/  
<br/>index.php  
<br/>login.php  
<br/>register.php  
<br/>cart.php  
<br/>checkout.php  
<br/>product.php  
<br/>shop.php  
<br/>profile.php  
<br/>orders.php  
<br/>wishlist.php  
<br/>contact.php  
<br/>about.php

# UI Design

Theme

Modern

Clean

Minimal

Responsive

Primary Color

Green (#2E7D32)

Secondary

White

Accent

Orange

Font

Poppins

Icons

Font Awesome

Rounded Cards

Smooth Animations

Bootstrap Components

# User Flow

Visitor

↓

Home

↓

Browse Products

↓

Product Details

↓

Register/Login

↓

Add to Cart

↓

Checkout

↓

Payment

↓

Order Success

↓

Track Order

# Admin Flow

Admin Login

↓

Dashboard

↓

Manage Products

↓

Manage Orders

↓

Manage Inventory

↓

Generate Reports

# Validation Rules

Email Unique

Password Minimum 8 Characters

Required Fields

Image Size Validation

Stock Cannot Be Negative

Coupon Expiry Check

# Security

Password Hashing

Prepared Statements

Session Authentication

Role Authorization

Input Sanitization

CSRF Protection

XSS Protection

Secure Logout

# Future Enhancements

Google Login

OTP Login

Online Payment Gateway

Email Notifications

SMS Notifications

AI Product Recommendation

Voice Search

PWA Support

Barcode Scanner

Multi Vendor Support

Delivery Boy Module

Admin Analytics

Mobile App

# Sample Modules

### Customer Module

- Registration
- Login
- Browse Products
- Wishlist
- Cart
- Checkout
- Orders
- Reviews
- Profile

### Product Module

- Categories
- Product Details
- Inventory
- Discounts
- Images

### Order Module

- Checkout
- Invoice
- Tracking
- Order History

### Admin Module

- Dashboard
- Products
- Categories
- Inventory
- Coupons
- Reports
- Customers

# Success Criteria

The project will be considered successful if:

- Users can register and log in securely.
- Customers can browse, search, filter, and purchase products.
- Cart and checkout function without errors.
- Admin can manage products, inventory, categories, coupons, and orders.
- Reports accurately reflect sales and inventory.
- The application is responsive across desktop, tablet, and mobile devices.
- Passwords are securely stored using hashing.
- Database operations use prepared statements to prevent SQL injection.
- The project follows a modular MVC-inspired folder structure.
- Code is well-organized, documented, and maintainable.

# Optional Advanced Features (Recommended for Higher Grades)

- Product recommendations ("Frequently Bought Together")
- Recently Viewed Products
- Flash Sale countdown timer
- Daily Deals section
- Loyalty points and rewards
- Referral code system
- Email invoice generation (PDF)
- Admin dashboard charts (Chart.js)
- Stock notifications for customers
- Product comparison
- Multiple product images with image gallery
- Pagination with AJAX
- Dark/Light mode toggle
- Activity logs for admin actions
- Export reports to Excel/PDF
- Responsive toast notifications
- Breadcrumb navigation
- Live search suggestions
- Featured and Trending products
- Related products on the product page
- User profile image upload
- SEO-friendly URLs

# Project Deliverables

- Responsive Grocery Mart Website
- PHP Source Code
- MySQL Database (.sql)
- Admin Panel
- User Panel
- Database ER Diagram
- Use Case Diagram
- Activity Diagram
- Sequence Diagram
- Class Diagram
- DFD (Level 0 & Level 1)
- UI Wireframes
- Test Cases
- Installation Guide
- User Manual
- Project Report (IEEE/University Format)
- Git Repository
- README Documentation

# Conclusion

Grocery Mart is designed as a complete, production-inspired e-commerce grocery platform tailored for a final-year university project. It demonstrates full-stack web development concepts including authentication, session management, CRUD operations, relational database design, secure coding practices, responsive UI development, and an administrative management system. The project balances academic feasibility with real-world functionality, making it suitable for implementation in PHP and MySQL while showcasing software engineering best practices.