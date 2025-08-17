import 'bootstrap';
import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
// import 'bootstrap/dist/css/bootstrap.min.css';
import '../styles/bootstrap_theme.min.css';
import '../styles/site.css';

//CLOCK IN FOOTER
    function showTime(){
        var date = new Date();
        var h = date.getHours(); // 0 - 23
        var m = date.getMinutes(); // 0 - 59
        var s = date.getSeconds(); // 0 - 59
        
        h = (h < 10) ? "0" + h : h;
        m = (m < 10) ? "0" + m : m;
        s = (s < 10) ? "0" + s : s;
        
        var time = h + ":" + m + ":" + s + " ";
        document.getElementById("blockToDisplayClock").innerText = time;
        document.getElementById("blockToDisplayClock").textContent = time;
        
        setTimeout(showTime, 1000);
        
    }
    showTime();
//END CLOCK