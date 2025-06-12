# MercadonLine Project Presentation

---

## Slide 1: Title Slide

- **Project:** MercadonLine
- **Description:** An online e-commerce platform.
- **Author:** [Your Name]

---

## Slide 2: Introduction

- **What is MercadonLine?**
  - A fully functional web application that simulates an online supermarket.
  - It allows users to browse products, add them to a cart, and complete a purchase.
  - It includes an administration panel for managing products, users, and orders.

---

## Slide 3: Technologies Used

- **Frontend:**
  - **Framework:** Vue.js 3
  - **Routing:** Vue Router
  - **HTTP Client:** Axios (or fetch) for API communication
  - **Styling:** CSS, possibly a framework like Bootstrap or Tailwind (based on `base.css` and `main.css`)

- **Backend:**
  - **Framework:** Symfony (PHP)
  - **API:** RESTful API
  - **ORM:** Doctrine for database interaction
  - **Security:** Symfony Security for authentication and authorization

- **Database:**
  - Likely a relational database like MySQL or PostgreSQL, managed by Doctrine.

- **Infrastructure:**
  - **Containerization:** Docker and Docker Compose for creating a consistent development and deployment environment.
  - **Web Server:** Nginx

---

## Slide 4: Core Features

- **User Authentication:**
  - Secure user registration and login.
  - JWT (JSON Web Tokens) for session management.

- **Product Catalog:**
  - View products by category.
  - Search functionality for finding specific products.
  - Product detail pages with images and descriptions.

- **Shopping Cart:**
  - Add/remove products from the cart.
  - View cart contents and total price.

- **Checkout Process:**
  - Create orders from the cart.
  - Integration with a payment system.

- **Admin Panel:**
  - Manage users (view, edit, delete).
  - Manage products (create, read, update, delete).
  - View and manage customer orders.
  - View application statistics.

---

## Slide 5: Project Architecture

- **Client-Server Model:**
  - Decoupled frontend and backend.
  - The **frontend** (Vue.js) is the client, running in the user's browser.
  - The **backend** (Symfony) is the server, providing data and services through a REST API.

- **API Communication:**
  - The frontend communicates with the backend via asynchronous HTTP requests.
  - JSON is used as the data exchange format.

---

## Slide 6: Database Schema

- A brief overview of the main entities:
  - `User`: Stores user information (name, email, password, roles).
  - `Product`: Stores product details (name, description, price, image, category).
  - `Category`: Organizes products into categories.
  - `Cart`: Represents a user's shopping cart.
  - `Order` & `OrderItem`: Stores information about completed purchases.
  - `Payment`: Stores payment details for orders.

---

## Slide 7: Key Code Snippets / Screenshots

*At this point, you can take screenshots of the application to show the UI.*

- **Recommended Screenshots:**
  - Home Page (`HomeView.vue`)
  - Product Listing (`CategoryView.vue`)
  - Product Detail Page (`ProductDetailView.vue`)
  - Shopping Cart (`CartView.vue`)
  - Login Page (`LoginView.vue`)
  - Admin Dashboard (`AdminStatsView.vue`)

---

## Slide 8: Conclusion & Future Work

- **Summary:**
  - MercadonLine is a robust e-commerce platform built with modern web technologies.
  - It demonstrates a clear separation of concerns between the frontend and backend.

- **Possible Future Improvements:**
  - Real-time notifications (e.g., for order status updates).
  - More advanced product search and filtering.
  - User reviews and ratings for products.
  - Integration of more payment gateways.
  - Deployment to a cloud provider.