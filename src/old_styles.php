<style>  
    body{
      padding: 0;
      margin: 0;
      font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
    }

    /* Header styles */
    header {      
      background-color: #66CDAA;
      margin-bottom: 50px;
      width: auto;
      height: auto;
    }

    /* Header container 1 */
    header .header-container1{
      display: flex;
      align-items: center;
      width: 100%;
      height: auto;
    }
    .logo-header a {
      margin-left: 20%;
    }
    .logo-header a, .logo-header a img {
      width: 54%;
    }
    .header-container1 ul {
      display: flex;
      list-style-type: none;
      width: 100%;
      height: auto;
      margin-right: 15%;
      justify-content: space-between;
      align-items: center;
    }
    .header-container1 ul li {
      margin: 0;
    }
    #search-header {
      list-style: none; /* Remove default list styling */
      width: 40%;
      margin: 0px 50px 0px 0px;
    }

    .search {
      padding-left: 2.5rem; /* Space for the icon */
      height: 38px; /* Adjust height as needed */
      border: 1px solid #ced4da; /* Bootstrap-like border */
      border-radius: 4px; /* Rounded corners */
      width: 100%; /* Full width */
      font-size: 16px;
      border: 0px;
    }
    .search-container {
      position: relative; /* Relative positioning for icon */
      display: inline-block; /* Adjust to fit content */
      width: 100%; /* Ensure it takes full width */
    }

    .bi-search {
      position: absolute; /* Absolute positioning inside input */
      top: 50%; /* Center vertically */
      left: 10px; /* Distance from left */
      transform: translateY(-50%); /* Align vertically */
      color: black; /* Icon color */
      pointer-events: none; /* Prevent icon from being clickable */
    }
    .bi-cart3 {
      font-size: 1.5rem; /* Tăng kích thước biểu tượng (có thể điều chỉnh: 1.5rem = 24px) */
      font-weight: bold; /* Làm biểu tượng đậm hơn */
      /* Nếu font-weight không đủ rõ, dùng filter để tăng độ đậm */
      filter: brightness(0.7); /* Tùy chọn: làm biểu tượng đậm hơn bằng cách giảm độ sáng */
    }
    header .header-container1 ul li img {
      display: block;
      max-height: 100px;
      width: auto;
      margin: 0 5px 0 20px;
      padding: 0;
    }
    .header-container1 li a {
      text-decoration: none;
      color:rgb(22, 52, 32);
    }
    .header-container1 li a:hover {
      text-decoration: underline;
    }

    /* Header container 2 */
    header .header-container2 {
      display: flex;
      align-items: center;
      width: 100%;
      padding: 10px 0;
    }
    .header-container2 ul {
      display: flex;
      list-style-type: none;
      margin: 0px;
      padding: 0px;
      width: 100%;
      justify-content: center;
      align-items: center;
    }
    .header-container2 ul li {
      cursor: default;
      position: relative;
      font-size: 16px;
      color:rgb(22, 52, 32);
      font-size: 19px;
      margin: 3.5px;
      padding: 15px 15px;
      border-radius: 5%;
    }
    .header-container2 ul li:hover {
      background-color:rgb(66, 128, 107);
      color:rgb(9, 26, 34);
      font-weight: bold;
    }
    .header-container2 ul a {
      text-decoration: none;
      color:rgb(22, 52, 32);
    }
    .header-container2 .brand .submenu {
      display: none;
      position: absolute;
      top: 100%;
      left: 0;
      background-color: #66CDAA;
      list-style: none;
      padding: 0;
      margin: 0;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      width: 200px;
      z-index: 1000;
      cursor: pointer;
    }
    .header-container2 .brand .submenu li {
      font-size: 15px;
    }
    .header-container2 .brand .submenu ul li:hover {
      background-color:rgb(66, 128, 107);
      color:rgb(9, 26, 34);
      font-weight: bold;
    }
    .header-container2 .brand:hover .submenu {
      display: block;
    }
    .header-container2 .brand li {
      padding: 10px 15px;
      color:rgb(22, 52, 32);
      font-size: 14px;
    }

    /* Footer styles */
    footer {      
      background-color: #66CDAA;
      height: 300px;
      margin-top: 30px;
    }
    footer .footer-container1 {
      display: flex;
      align-items: center;
      width: 100%;
      padding: 10px 20px;
    }
    .footer-container1 div {
      justify-content: space-between;
      align-items: center;
    }
    .footer-container1 ul {
      list-style-type: none;
    }

    /* Index style */
    .container-index {
      margin: 0 8%;
    }
    .hot {
      height: 500px;
    }
    .hot h1 {
      margin-bottom: 40px;
      text-align: center;
    }
    .hot .hot-images-wrapper {
      display: flex;
      justify-content: center;
      height: auto;
    }
    .hot .hot-images-wrapper a {
      margin: 0 10px;
    }
    .hot .hot-images-wrapper img {
      border-radius: 25px;
      min-height: 50px;
      max-height: 720px;
      width: 100%;
      object-fit: scale-down;
      object-position: center;
    }

    /* Apple */
    .container-products-apple {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
            justify-content: center;
    }
    .product-apple {
        flex: 0 0 calc(20% - 16px); /* 5 sản phẩm mỗi hàng, trừ khoảng cách */
        min-width: 200px;
        max-width: 220px;
        margin-bottom: 20px;
        transition: transform 0.3s ease;
    }
    .product-apple:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    .product-apple a {
      text-decoration: none;
      color: black;
    }
    .product-apple img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px 8px 0 0;
    }
    .product-apple .card-body {
        text-align: center;
    }
    .product-apple .card-title {
        font-size: 1.1rem;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .product-apple .card-text.price {
        color: #e74c3c;
        font-size: 1rem;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .product-apple .card-text.features {
        font-size: 0.9rem;
        color: #555;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 3; /* Giới hạn 3 dòng */
        -webkit-box-orient: vertical;
    }
    @media (max-width: 1200px) {
        .product-apple {
            flex: 0 0 calc(25% - 15px); /* 4 sản phẩm mỗi hàng */
        }
    }
    @media (max-width: 768px) {
        .product-apple {
            flex: 0 0 calc(33.33% - 13.33px); /* 3 sản phẩm mỗi hàng */
        }
    }
    @media (max-width: 576px) {
        .product-apple {
            flex: 0 0 calc(50% - 10px); /* 2 sản phẩm mỗi hàng */
        }
    }

    /* Detail */
    .container-detail {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .product-detail {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .product-image {
            flex: 1;
            min-width: 300px;
        }
        .product-image img {
            max-width: 100%;
            border-radius: 8px;
        }
        .product-info {
            flex: 1;
            min-width: 300px;
        }
        .product-info h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .brand-logo img {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .price {
            color: #e44d26;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .description {
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .specifications {
            margin-bottom: 20px;
        }
        .specifications h3 {
            font-size: 20px;
            margin-bottom: 10px;
        }
        .specifications ul {
            list-style: none;
            padding: 0;
        }
        .specifications ul li {
            padding: 5px 0;
        }
        .buy-button {
            background-color: #28a745;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .buy-button:hover {
            background-color: #218838;
        }
        .quantity-input {
            padding: 8px;
            width: 60px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        @media (max-width: 768px) {
            .product-detail {
                flex-direction: column;
            }
            .product-image, .product-info {
                min-width: 100%;
            }
        }
     /* Sign up */
    .sign-up-success {
      text-align: center;
      margin: 100px 0 200px 0;
    }
    .label-sign-up {
      width: 30%;
      padding-left: 5%;
    }
    .input-sign-up {
      width: 60%;
    }
    /* Sign in styles */
    .form-sign-in div {
      display: flex;
      margin: 10px;
    }
    .form-sign-in label {
      display: block;
      width: 150px;
    }
    .form-sign-in button {
    }
</style>