<style>
/*
Theme Name: PelotonU
Author: Brent Jett, Nehmedia
Version: 0.1
*/

h1 {
    font-size:30px;
    color:#00305A;
    font-family:'Ubuntu', Helvetica, sans-serif;
}
h2 {
    color: #80A74C;
    font-size: 26px;
    font-family:'Ubuntu', Helvetica, sans-serif;
    font-weight: 300;
}
h3 {
    color: #80A74C;
    font-size: 22px;
    font-family:'Ubuntu', Helvetica, sans-serif;
    font-weight: 300;
}
h1:first-child,
h2:first-child,
h3:first-child {
    margin-top:0px;
}

img {
  max-width: 100%;
  height: auto;
  width: auto;
}

/* header */
#brand {
    display:inline-block;
}

.box {
    display:block;
    position:relative;
}
a.box {
    display:inline-block;
}
.box:before {
    display:block;
    content: "";
    padding-bottom:100%;
}
.box > div {
    position:absolute;
    top:0px;
    left:0px;
    right:0px;
    bottom:0px;
}
body > header {
  background: #00305A;
  background: rgba(0, 48, 90, 0.95);
  color: white;
  padding: 20px 0px;
  text-align:center;
}
@supports (-webkit-backdrop-filter: blur(20px)) {
    body > header {
        -webkit-backdrop-filter: blur(20px) saturate(300%);
        background: rgba(0, 48, 90, 0.85);
    }
}

/* Hero */
.green-header,
.white-header {
    padding:10px;
    background:white;
    float:left;
    clear:both;
    background-color:#80A74C;
    background-color:rgba(128, 167, 76, 0.9);
    color:white;
    margin:0px;
    font-weight:200;
}
.white-header {
    background-color:white;
    background-color:rgba(255, 255, 255, 0.93);
    color:#80A74C;
    font-size:22px;
}

/* General Content */
article {
    padding:30px 0px;
}
body.no-top-padding article {
    padding-top:0px;
}
body.no-bottom-padding article {
    padding-bottom:0px;
}


/* Homepage Content */
.debt-free-image-widget div {
  text-align: center;
}

.debt-free-image-widget img {
  display: inline-block !important;
  /* Must display inline-block to center image */;
}

/* Nav Menu */
.menu-main-container {
    margin-top:10px;
}
.wp-nav-menu {
	list-style:none;
    margin:0px;
    padding:0px;
    font-family:'Ubuntu', Helvetica, sans-serif;
    text-align:center;
}
.wp-nav-menu > li {
    display:inline-block;
}
.wp-nav-menu > li a {
    display:block;
    padding:10px;
    color:#aaa;
    font-size:18px;
}
.wp-nav-menu li.highlight a {
    background:#80A74C;
    color:white;
    border-radius:3px;
}
.wp-nav-menu li.current-menu-item a {
    color:white;
}

@media screen and (min-width:736px){
    #brand {
        float:left;
    }
    .menu-main-container {
        float: right;
        margin-top:3px;
    }
    body {
      margin-top: 94px;
    }
    body.home,
    body.home.admin-bar {
        margin-top:0px;
    }
    body > header {
        position: fixed;
        top: 0px;
        left: 0px;
        right: 0px;
        z-index: 99999;
        text-align:left;
    }

    body.admin-bar > header {
        top: 32px;
    }
}
@media screen and (min-width:768px){
    .biglink {
        font-size:24px;
        padding:5px 0px;
    }

    article {
        padding:50px 0px;
    }
    body.no-top-padding article {
        padding-top:0px;
    }
    body.no-bottom-padding article {
        padding-bottom:0px;
    }
}
</style>
<header>
	<div class="container">
		<a id="brand" href="http://<?= URL ?>">
			<img src="/imgs/logo.png">
		</a>
		<nav class="menu-main-container">
			<ul id="menu-main" class="wp-nav-menu">
				<li <?php if ($_SERVER["REQUEST_URI"] == "/timeclock") {echo "class='current-menu-item'";} ?>>
					<a href="http://<?= URL ?>/timeclock">Clock In/Out</a>
				</li>
				<li <?php if ($_SERVER["REQUEST_URI"] == "/student/logs") {echo "class='current-menu-item'";} ?>>
					<a href="http://<?= URL ?>/student/logs">Time Logs</a>
				</li>
        <li>
          <a href="<?= $_SESSION['calendly']?>"><span class="glyphicon glyphicon-calendar" data-toggle="tooltip" data-placement="bottom" title="Schedule a Mentor Meeting"></span></a>
        </li>
				<li class="highlight">
					<a href="http://<?= URL ?>/logout">Logout</a>
				</li>
			</ul>
		</nav>
	</div>
</header>