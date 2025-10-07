<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ayura Hampers - Home</title>
  <style>
    /* Reset & base */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #e6f2ff;
      color: #1a3e59;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      animation: fadeIn 1s ease forwards;
      position: relative;
      overflow-x: hidden;
    }

    /* Floating bubbles background */
    .bubble {
      position: fixed;
      bottom: -100px;
      background: rgba(58, 145, 191, 0.15);
      border-radius: 50%;
      animation: floatUp linear infinite;
      pointer-events: none;
      opacity: 0.6;
      filter: blur(1px);
      z-index: 0;
    }
    @keyframes floatUp {
      0% {
        transform: translateY(0) scale(1);
        opacity: 0.6;
      }
      50% {
        opacity: 0.8;
      }
      100% {
        transform: translateY(-120vh) scale(1.2);
        opacity: 0;
      }
    }

    header {
      background: #3a91bf;
      padding: 20px 40px;
      box-shadow: 0 3px 8px rgba(58, 145, 191, 0.4);
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    header h1 {
      font-weight: 700;
      font-size: 1.9rem;
      cursor: default;
      letter-spacing: 2px;
      color: #f0f9ff;
      animation: slideInFromLeft 1s ease forwards;
      user-select: none;
    }
    nav {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    nav > a {
      text-decoration: none;
      color: #e1f0ff;
      font-weight: 600;
      position: relative;
      font-size: 1rem;
      padding: 6px 8px;
      transition: color 0.3s ease;
      user-select: none;
    }
    nav > a:hover,
    nav > a:focus {
      color: #a6d1ff;
    }
    nav > a::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      background: #a6d1ff;
      bottom: -5px;
      left: 0;
      transition: width 0.3s ease;
      border-radius: 2px;
    }
    nav > a:hover::after,
    nav > a:focus::after {
      width: 100%;
    }

    main {
      flex-grow: 1;
      padding: 40px 20px;
      max-width: 960px;
      margin: 0 auto;
      animation: fadeInUp 1s ease forwards;
      text-align: center;
      position: relative;
      z-index: 10;
    }
    main h2 {
      font-size: 2.4rem;
      margin-bottom: 15px;
      border-bottom: 3px solid #3a91bf;
      display: inline-block;
      padding-bottom: 5px;
      color: #1a3e59;
      user-select: none;
    }
    main p {
      font-size: 1.15rem;
      line-height: 1.6;
      margin-bottom: 30px;
      color: #2a557f;
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
    }

    /* Slider container */
    .slider {
      position: relative;
      width: 100%;
      overflow: hidden;
      margin-top: 30px;
      user-select: none;
    }
    .slider-track {
      display: flex;
      gap: 25px;
      transition: transform 0.5s ease;
      will-change: transform;
      padding-bottom: 10px;
    }

    .card {
      background: #ffffff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 10px 25px rgba(58, 145, 191, 0.15);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      width: 320px;
      text-align: center;
      animation: fadeInUp 1s ease;
      user-select: none;
      flex-shrink: 0;
      position: relative;
    }
    .card img {
      width: 100%;
      height: 210px;
      object-fit: cover;
      border-bottom: 3px solid #3a91bf;
      transition: transform 0.3s ease;
    }
    .card:hover img {
      transform: scale(1.1);
    }
    .card h3 {
      font-size: 1.4rem;
      margin: 18px 0 12px;
      color: #2a557f;
    }
    .card p {
      font-size: 1rem;
      padding: 0 20px 25px;
      color: #486d8a;
    }
    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 18px 35px rgba(58, 145, 191, 0.3);
      z-index: 1;
    }
    .price {
      font-weight: 700;
      color: #1a3e59;
      font-size: 1.2rem;
      margin: 12px 0;
    }

    /* Slider arrows */
    .slider-arrow {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: #3a91bf;
      color: #f0f9ff;
      border: none;
      border-radius: 50%;
      width: 42px;
      height: 42px;
      cursor: pointer;
      font-size: 1.8rem;
      font-weight: bold;
      box-shadow: 0 4px 12px rgba(58, 145, 191, 0.5);
      transition: background 0.3s ease;
      user-select: none;
      z-index: 20;
    }
    .slider-arrow:hover {
      background: #2a6e9e;
    }
    .slider-arrow:disabled {
      opacity: 0.3;
      cursor: default;
    }
    .slider-arrow.left {
      left: 5px;
    }
    .slider-arrow.right {
      right: 5px;
    }

    .btn {
      background: #3a91bf;
      color: #fff;
      padding: 14px 32px;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      font-weight: 700;
      font-size: 1rem;
      transition: background 0.4s ease, box-shadow 0.3s ease;
      box-shadow: 0 6px 12px rgba(58, 145, 191, 0.4);
      user-select: none;
      margin-bottom: 30px;
      position: relative;
      overflow: hidden;
    }
    .btn:hover {
      background: #2a6e9e;
      box-shadow: 0 10px 20px rgba(42, 110, 158, 0.6);
      transform: translateY(-4px);
    }

    /* Ripple effect on buttons */
    .btn:after {
      content: "";
      position: absolute;
      background: rgba(255, 255, 255, 0.4);
      border-radius: 50%;
      width: 100px;
      height: 100px;
      top: 50%;
      left: 50%;
      pointer-events: none;
      transform: translate(-50%, -50%) scale(0);
      opacity: 0;
      transition: transform 0.6s ease, opacity 1s ease;
      z-index: 0;
    }
    .btn:active:after {
      transform: translate(-50%, -50%) scale(1);
      opacity: 1;
      transition: 0s;
    }

    footer {
      text-align: center;
      padding: 24px 12px;
      background: #3a91bf;
      color: #f0f9ff;
      font-weight: 700;
      letter-spacing: 1.2px;
      user-select: none;
      animation: fadeIn 1.2s ease forwards;
      margin-top: auto;
    }

    /* Animations */
    @keyframes fadeIn {
      from {opacity: 0;}
      to {opacity: 1;}
    }
    @keyframes fadeInUp {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }
    @keyframes slideInFromLeft {
      from {opacity: 0; transform: translateX(-30px);}
      to {opacity: 1; transform: translateX(0);}
    }

    .toast {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background-color: #2196f3;
      color: white;
      padding: 14px 26px;
      border-radius: 24px;
      font-weight: 700;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.5s ease, transform 0.5s ease;
      box-shadow: 0 5px 14px rgba(33, 150, 243, 0.5);
      z-index: 9999;
      user-select: none;
    }
    .toast.show {
      opacity: 1;
      transform: translateY(-12px);
      pointer-events: auto;
    }

    /* Responsive */
    @media (max-width: 700px) {
      nav {
        flex-wrap: wrap;
        justify-content: center;
      }
      .card {
        width: 90vw !important;
      }
      .slider-arrow {
        display: none;
      }
    }
  </style>
</head>
<body>

  <!-- Floating bubbles (random sizes and left positions) -->
  <div class="bubble" style="width: 60px; height: 60px; left: 10vw; animation-duration: 30s; animation-delay: 0s;"></div>
  <div class="bubble" style="width: 45px; height: 45px; left: 25vw; animation-duration: 25s; animation-delay: 5s;"></div>
  <div class="bubble" style="width: 80px; height: 80px; left: 40vw; animation-duration: 40s; animation-delay: 3s;"></div>
  <div class="bubble" style="width: 50px; height: 50px; left: 60vw; animation-duration: 32s; animation-delay: 7s;"></div>
  <div class="bubble" style="width: 35px; height: 35px; left: 75vw; animation-duration: 28s; animation-delay: 2s;"></div>

  <header>
    <h1>Ayura Hampers</h1>
    <nav>
      <a href="index.php">Home</a>
      <a href="about.html">About</a>
      <a href="contact.php">Contact</a>

      <?php if (isset($_SESSION['user_email'])): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
      <?php endif; ?>

      <a href="faq.html">FAQ</a>
      <a href="cart.html">Cart</a>
    </nav>
  </header>

  <main>
    <h2>Welcome to Ayura Hampers</h2>
    <p>
      Discover the finest curated gift hampers perfect for every occasion.
      From birthdays to weddings, our handpicked collections are crafted with love and care to bring smiles to your loved ones.
    </p>
    <button class="btn" onclick="location.href='about.html'">Learn More About Us</button>

    <div class="slider" aria-label="Featured Hampers">
      <button class="slider-arrow left" aria-label="Previous">&#10094;</button>
      <div class="slider-track">
        <div class="card" tabindex="0">
          <img src="img/h1.jpg" alt="Luxury Festive Hamper" />
          <h3>Luxury Festive Hamper</h3>
          <p>Celebrate special moments with this beautifully packed premium hamper.</p>
          <p class="price">₹99</p>
          <button class="btn" onclick="addToCart('Luxury Festive Hamper', 'img/h1.jpg', 99)">Add to Cart</button>
        </div>

        <div class="card" tabindex="0">
          <img src="img/h2.jpg" alt="Chocolate Delight" />
          <h3>Chocolate Delight</h3>
          <p>Perfect treat box filled with handcrafted chocolates and sweets.</p>
          <p class="price">₹99</p>
          <button class="btn" onclick="addToCart('Chocolate Delight', 'img/h2.jpg', 99)">Add to Cart</button>
        </div>
      </div>
      <button class="slider-arrow right" aria-label="Next">&#10095;</button>
    </div>
  </main>

  <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">Item added!</div>

  <script>
    // Cart & Toast
    function addToCart(name, image, price) {
      let cart = JSON.parse(localStorage.getItem("cart")) || [];
      let existingItem = cart.find(item => item.name === name);
      if (existingItem) {
        existingItem.quantity += 1;
        existingItem.total = existingItem.quantity * existingItem.price;
      } else {
        cart.push({ name, image, price, quantity: 1, total: price });
      }
      localStorage.setItem("cart", JSON.stringify(cart));
      showToast(`${name} added to cart`);
    }

    function showToast(msg) {
      let toast = document.getElementById("toast");
      toast.innerText = msg;
      toast.classList.add("show");
      setTimeout(() => toast.classList.remove("show"), 3000);
    }

    // Slider logic
    (() => {
      const track = document.querySelector('.slider-track');
      const cards = [...track.children];
      const leftBtn = document.querySelector('.slider-arrow.left');
      const rightBtn = document.querySelector('.slider-arrow.right');

      let currentIndex = 0;
      const maxIndex = cards.length - 1;
      const cardWidth = cards[0].offsetWidth + 25; // width + gap

      function updateSlider() {
        track.style.transform = `translateX(${-currentIndex * cardWidth}px)`;
        leftBtn.disabled = currentIndex === 0;
        rightBtn.disabled = currentIndex >= maxIndex;
      }

      leftBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
          currentIndex--;
          updateSlider();
        }
      });

      rightBtn.addEventListener('click', () => {
        if (currentIndex < maxIndex) {
          currentIndex++;
          updateSlider();
        }
      });

      // Optional: auto slide every 7 seconds
      setInterval(() => {
        currentIndex = (currentIndex >= maxIndex) ? 0 : currentIndex + 1;
        updateSlider();
      }, 7000);

      updateSlider();
    })();
  </script>

  <footer>
    &copy; 2025 Ayura Hampers. All rights reserved.
  </footer>
</body>
</html>
