
<!-- animated bg starts  -->
<style>
    
    .navbar {
        background: linear-gradient(to right, #000000, #141414, #171010, #170909, #170909, #000000, #000000, #000000, #1f0e0e, #170909, #171010, #141414, #000000);
        background-size: 200% 100%;
        animation: shiftBackground 10s linear infinite;
    }

    /* Keyframes for the background gradient animation */
    @keyframes shiftBackground {
        0% {
            background-position: 0% 0%;
        }
        100% {
            background-position: 100% 0%;
        }
    }
</style>
<nav class="navbar">
    <a href="index.php"> <i class="fas fa-home"></i> <span>home</span> </a>
    <a href="about.php"> <i class="fas fa-user"></i> <span>about</span> </a>
    <a href="portfolio.php"> <i class="fas fa-briefcase"></i> <span>portfolio</span> </a>
    <a href="work.php"> 
        <img src="assets/img/icons/cool.png" alt="">
    </i> <span>fun works</span> </a>
    <a href="contact.php"> <i class="fas fa-address-book"></i> <span>contact</span> </a>
    
</nav>

<style>
    .navbar a img{
        width:17px;
    }
</style>
