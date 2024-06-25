const tempCard = document.getElementById('temp-card');
const lightCard = document.getElementById('light-card');
const humCard = document.getElementById('hum-card');
    
const popupTemp = document.getElementById('popup-temp');
const popupLight = document.getElementById('popup-light');
const popupHum = document.getElementById('popup-hum');

const btnTemp = document.getElementById('btn-temp');
const btnLight = document.getElementById('btn-light');
const btnHum = document.getElementById('btn-hum');
    
const inputTemp = document.getElementById('input-temp');
const inputLight = document.getElementById('input-light');
const inputHum = document.getElementById('input-hum');

const popupContainer = document.querySelector('.popup-container');

document.addEventListener('DOMContentLoaded', function() {
    tempCard.addEventListener('click', function() {
        showPopup(popupTemp);
    });

    lightCard.addEventListener('click', function() {
        showPopup(popupLight);
    });

    humCard.addEventListener('click', function() {
        showPopup(popupHum);
    });

    btnTemp.addEventListener('click', function() {
        if(inputTemp.value != ''){
            tempMax= inputTemp.value;
            postPhp({temp: tempMax});
        }
        inputTemp.value='';
        checkValue();
        hidePopups();
    });

    btnLight.addEventListener('click', function() {
        if(inputLight.value != ''){
            lightMax = inputLight.value;
            postPhp({light: lightMax});
        }
        inputLight.value='';
        checkValue();
        hidePopups();
    });

    btnHum.addEventListener('click', function() {
        if(inputHum.value != ''){
            humMax = inputHum.value;
        }
        switch(humMax){
            case 'basso': humMax = 1;break;
            case 'medio': humMax = 2;break;
            case 'alto': humMax = 3;break;
        }
        postPhp({hum: humMax});
        checkValue();
        hidePopups();
    });
    
    document.addEventListener('click', function(e) {
        if (popupContainer.classList.contains('active')) {
            const popups = [popupTemp, popupLight, popupHum];
            let clickedInsidePopup = false;

            popups.forEach(function(popup) {
                if (popup.classList.contains('active') && e.target.closest('.popup-box') === popup) {
                    clickedInsidePopup = true;
                }
            });
            if (!clickedInsidePopup && !e.target.closest('.card')) {
                hidePopups();
            }
        }   
    });

    function showPopup(popup) {
        hidePopups();
        popupContainer.classList.add('active');
        popup.classList.add('active');
    }

    function hidePopups() {
        const popups = [popupTemp, popupLight, popupHum];
        popups.forEach(function(popup) {
            popup.classList.remove('active');
        });
        popupContainer.classList.remove('active');
    }

    function postPhp(value){
        const options = {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json' 
            },
            body: JSON.stringify(value)
        };
        fetch('index.php', options)
    }
});

function checkValue(){
    if(temp>tempMax)
        tempCard.classList.add('red');
    else
        tempCard.classList.remove('red');

    if(light>lightMax)
        lightCard.classList.add('red');
    else
        lightCard.classList.remove('red');

    switch(humMax){
        case 1:
            if(!(hum > 0 && hum<30))
                humCard.classList.add('red');
            break;
        case 2:
            if(!(hum >= 30 && hum<70))
                humCard.classList.add('red');
            break;
        case 3:
            if(!(hum >= 70 && hum<=100))
                humCard.classList.add('red');
            break;
    }
}
checkValue();

