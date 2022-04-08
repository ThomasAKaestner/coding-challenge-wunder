<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
<style>
    * {
        box-sizing: border-box;
    }

    body {
        background-color: #f1f1f1;
    }

    #regForm {
        background-color: #ffffff;
        margin: 100px auto;
        font-family: Raleway;
        padding: 40px;
        width: 70%;
        min-width: 300px;
    }

    h1 {
        text-align: center;
    }

    input {
        padding: 10px;
        width: 100%;
        font-size: 17px;
        font-family: Raleway;
        border: 1px solid #aaaaaa;
    }

    /* Mark input boxes that gets an error on validation: */
    input.invalid {
        background-color: #ffdddd;
    }

    /* Hide all steps by default: */
    .tab {
        display: none;
    }

    button {
        background-color: #1a202c;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        font-size: 17px;
        font-family: Raleway;
        cursor: pointer;
    }

    button:hover {
        opacity: 0.8;
    }

    #prevBtn {
        background-color: #bbbbbb;
    }

    /* Make circles that indicate the steps of the form: */
    .step {
        height: 15px;
        width: 15px;
        margin: 0 2px;
        background-color: #bbbbbb;
        border: none;
        border-radius: 50%;
        display: inline-block;
        opacity: 0.5;
    }

    .step.active {
        opacity: 1;
    }

    /* Mark the steps that are finished and valid: */
    .step.finish {
        background-color: #939393;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
        crossorigin="anonymous"></script>
<body>
<form id="regForm">
    <div>
        <img src="https://www.wundermobility.com/uploads/redesign/logo-darkblue.svg"
             style=" display:block;margin:auto;">
    </div>
    <h1 id="mainTitle">Register:</h1>
    <!-- One "tab" for each step in the form: -->
    <div class="tab" id="step0">Name:
        <p><input
                  placeholder="First name..." onkeyup="saveDataToLocalStorage('firstName')"
                  oninput="this.className = ''" id="firstName"
                  name="firstName"></p>
        <p><input placeholder="Last name..." onkeyup="this.className = ''"
                  onkeydown="saveDataToLocalStorage('lastName')"
                  id="lastName"
                  name="lastName"></p>
        <p><input placeholder="Phone..."
                  onkeydown="saveDataToLocalStorage('telephone')"
                  id="telephone"
                  oninput="this.className = ''" name="telephone"></p>
    </div>
    <div class="tab" id="step1">Address Info:
        <p><input placeholder="Street..."
                  onkeyup="saveDataToLocalStorage('street')"
                  id="street"
                  oninput="this.className = ''" name="street"></p>
        <p><input placeholder="Housenumber..."
                  onkeyup="saveDataToLocalStorage('house_number')"
                  id="house_number"
                  oninput="this.className = ''" name="house_number"></p>
        <p><input placeholder="City..."
                  id="city"
                  onkeyup="saveDataToLocalStorage('city')"
                  oninput="this.className = ''" name="city"></p>
        <p><input placeholder="Zip Code..."
                  id="zip_code"
                  onkeyup="saveDataToLocalStorage('zip_code')"
                  oninput="this.className = ''" name="zip_code"></p>
    </div>
    <div class="tab" id="step2">Payment Info:
        <p><input placeholder="Account Owner Name..."
                  id="owner"
                  onkeyup="saveDataToLocalStorage('owner')"
                  oninput="this.className = ''" name="owner"></p>
        <p><input placeholder="IBAN..."
                  id="iban"
                  onkeyup="saveDataToLocalStorage('iban')"
                  oninput="this.className = ''" name="iban"></p>
    </div>
    <div id="success" style="display: none">
        <p>You successfully registered your payment data.</p>
        <p>Your paymentdataId is:<span id="paymentDataIdOutPut"></span></p>
    </div>
    <div id="failure" style="display: none">
        <p>An issue occurred. Please try again.</p>
    </div>
    <div style="overflow:auto;">
        <div style="float:right;">
            <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
            <button type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
        </div>
    </div>
    <!-- Circles which indicates the steps of the form: -->
    <div style="text-align:center;margin-top:40px;" id="steps">
        <span class="step"></span>
        <span class="step"></span>
        <span class="step"></span>
    </div>
</form>

<script>
    var currentTab = 0; // Current tab is set to be the first tab (0)
    showTab(currentTab); // Display the current tab

    function showTabFromLocalStorage(step) {
        $('.tab').hide();
        $('#step'+step+'').show();
        showTab(step);
    }

    $(document).ready(function () {
        if (localStorage.getItem("step") !== null ) {
            showTabFromLocalStorage(localStorage.getItem("step"));
            currentTab = localStorage.getItem("step");
        }
        setLocalStorageData('firstName');
        setLocalStorageData('lastName');
        setLocalStorageData('telephone');
        setLocalStorageData('street');
        setLocalStorageData('house_number');
        setLocalStorageData('city');
        setLocalStorageData('zip_code');
        setLocalStorageData('owner');
        setLocalStorageData('iban');
    });

    function showTab(n) {
        // This function will display the specified tab of the form...
        var x = document.getElementsByClassName("tab");

        x[n].style.display = "block";
        //... and fix the Previous/Next buttons:
        if (n == 0) {
            document.getElementById("prevBtn").style.display = "none";
        } else {
            document.getElementById("prevBtn").style.display = "inline";
        }
        if (n == (x.length - 1)) {
            document.getElementById("nextBtn").innerHTML = "Submit";
        } else {
            document.getElementById("nextBtn").innerHTML = "Next";
        }
        //... and run a function that will display the correct step indicator:
        fixStepIndicator(n)
    }

    function nextPrev(n) {
        // This function will figure out which tab to display
        var x = document.getElementsByClassName("tab");
        // Exit the function if any field in the current tab is invalid:
        if (n == 1 && !validateForm()) return false;
        // Hide the current tab:
        x[currentTab].style.display = "none";
        // first
        // Increase or decrease the current tab by 1:
        currentTab = parseInt(currentTab) + n;
        // save to local storage
        // if you have reached the end of the form...
        if (currentTab >= x.length) {
            // ... the form gets submitted:
            let myform = document.getElementById("regForm");
            let fd = new FormData(myform);
            $.ajax({
                url: "/api/signup",
                data: fd,
                cache: false,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function (dataofconfirm) {
                    $('#paymentDataIdOutPut').text(dataofconfirm);
                    document.getElementById("mainTitle").innerHTML = "Success";
                    document.getElementById("success").style.display = "inline";
                    document.getElementById("prevBtn").style.display = "none";
                    document.getElementById("nextBtn").style.display = "none";
                    document.getElementById("steps").style.display = "none";
                    localStorage.clear();
                },
                error: function (dataofconfirm) {
                    document.getElementById("mainTitle").innerHTML = "Error";
                    document.getElementById("failure").style.display = "inline";
                    document.getElementById("prevBtn").style.display = "none";
                    document.getElementById("nextBtn").style.display = "none";
                    document.getElementById("steps").style.display = "none";
                }
            });
            return false;
        }

        localStorage.setItem("step", currentTab);


        // Otherwise, display the correct tab:
        showTab(currentTab);
    }

    function validateForm() {
        // This function deals with validation of the form fields
        var x, y, i, valid = true;
        x = document.getElementsByClassName("tab");
        y = x[currentTab].getElementsByTagName("input");
        // A loop that checks every input field in the current tab:
        for (i = 0; i < y.length; i++) {
            // If a field is empty...
            if (y[i].value == "") {
                // add an "invalid" class to the field:
                y[i].className += " invalid";
                // and set the current valid status to false
                valid = false;
            }
        }
        // If the valid status is true, mark the step as finished and valid:
        if (valid) {
            document.getElementsByClassName("step")[currentTab].className += " finish";
        }
        return valid; // return the valid status
    }

    function saveDataToLocalStorage(id) {
        var value = $('#' + id + '').val();
        localStorage.setItem(id, value)
    }


    function fixStepIndicator(n) {
        // This function removes the "active" class of all steps...
        var i, x = document.getElementsByClassName("step");
        for (i = 0; i < x.length; i++) {
            x[i].className = x[i].className.replace(" active", "");
        }
        //... and adds the "active" class on the current step:
        x[n].className += " active";
    }

    function setLocalStorageData(id)
    {
        if(localStorage.getItem(id) !== null && localStorage.getItem(id) !== undefined)
        {
            document.getElementById(id).value = localStorage.getItem(id);
        }
    }
</script>
</body>
</html>
