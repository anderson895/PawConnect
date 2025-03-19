<?php

session_start();
include('api/config/class.php');

$db = new global_class();



if (empty($_SESSION)) {


    if (isset($_GET['components'])) {
        header('Location: index.php?page=home');
    } else {
        $_GET = ['page' => 'LogReg'] + $_GET;
    }
    
}else{

   
    
    $UserID = $_SESSION['UserID'] ?? null;

    if ($UserID) {
        $session_data = $db->check_account($UserID);
    
        if (!empty($session_data) && is_array($session_data) && isset($session_data[0])) {
            $_SESSION['name'] = $session_data[0]['Name'] ?? '';
            $_SESSION['email'] = $session_data[0]['Email'] ?? '';
            $_SESSION['username'] = $session_data[0]['Username'] ?? '';
            $_SESSION['ProfilePic'] = $session_data[0]['ProfilePic'] ?? '';
            $_SESSION['Role'] = $session_data[0]['Role'] ?? '';
            $_SESSION['BirthDate'] = $session_data[0]['BirthDate'] ?? '';
            $_SESSION['Contact'] = $session_data[0]['Contact'] ?? '';
            $_SESSION['Address'] = $session_data[0]['Address'] ?? '';
            $_SESSION['Gender'] = $session_data[0]['Gender'] ?? '';
            $_SESSION['Link_address'] = $session_data[0]['Link_address'] ?? '';
            $_SESSION['Bio'] = $session_data[0]['Bio'] ?? '';
        } else {
            // Handle invalid session data
            session_destroy();
            header('Location: index.php?page=LogReg');
            exit();
        }
    } else {
        // Redirect to login if no UserID is found
        session_destroy();
        header('Location: index.php?page=LogReg');
        exit();
    }
    

    
   
}

// Autoload components and pages
function loadComponent($component)
{
    $path = __DIR__ . "/components/{$component}.php";
    if (file_exists($path)) {
        include $path;
    }
}

function loadPage($folder, $page)
{
    $path = __DIR__ . "/{$folder}/{$page}.php";

    // Load the page if it exists, otherwise load Home as default
    if (file_exists($path)) {
        include $path;
    } else {
        include __DIR__ . '/pages/Home.php'; // Default to Home if page is not found
    }
}

// Get 'page', 'vetpage', and 'lgupage' parameters if they exist
$page = $_GET['page'] ?? 'LogReg';         // Default to 'home' if 'page' is not set
$vetpage = $_GET['vetpages'] ?? null;     // Check if 'vetpage' is set
$lgupage = $_GET['lgupages'] ?? null;     // Check if 'lgupage' is set
$component = $_GET['components'] ?? null;

// Include the appropriate Navbar component
if ($vetpage) {
    loadComponent('VetNavbar');          // Load VetNavbar for vet pages
} elseif ($lgupage) {
    loadComponent('LGUNavbar');          // Load LGUNavbar for LGU pages
} elseif ($page !== 'LogReg') {
    loadComponent('Navbar');             // Load default Navbar if it's not the login page
}

// Always load the Floating component unless it's the login page
if ($page !== 'LogReg') {
    loadComponent('Floating');
}



if(!empty($_GET['role'])){
    if($_GET['role']=='pet_owner'){
        loadComponent('Navbar');      
    }else if($_GET['role']=='vet'){
        loadComponent('VetNavbar'); 
    }else if($_GET['role']=='lgu'){
        loadComponent('LGUNavbar');    
    }  
}


// Determine which page to load
if ($component) {
    loadComponent(ucfirst($component)); 
    loadComponent('Floating');
} elseif ($vetpage) {
    loadComponent('Floating');
    loadPage('vetpages', $vetpage);
} elseif ($lgupage) {
    loadComponent('Floating');
    loadPage('lgupages', $lgupage);
} else {
    loadPage('pages', ucfirst($page)); 
    
}
