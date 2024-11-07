/**
 *      UNICORN ALERT
 *      Author:             Jonas Riemer
 *      URL:                https://riemerjonas.de
 *      Version:            1.0
 */

// Unicorn-Alert Types
const UnicornAlertTypes = {
    SUCCESS: 'alert_success',
    ERROR: 'alert_error',
    WARNING: 'alert_warning',
    INFO: 'alert_info'
};


// FARBSHEMA
const UnicornAlertColors = {
    BORDER_SUCCESS: '#3fab3e',
    BORDER_ERROR: '#e74c3c',
    BORDER_WARNING: '#f39c12',
    BORDER_INFO: '#3498db',

    BACKGROUND_SUCCESS: '#70ff6e',
    BACKGROUND_ERROR: '#ffa1a1',
    BACKGROUND_WARNING: '#fff1a1',
    BACKGROUND_INFO: '#a1e3ff',

    TEXT_SUCCESS: '#2e5e2d',
    TEXT_ERROR: '#5e2d2d',
    TEXT_WARNING: '#6b4912',
    TEXT_INFO: '#123f6b'
};


class UnicornAlertHandler
{
    constructor()
    {
        this.alerts = [];
        this.implementStyles(); 
        this.frame = this.createFrame();
    }


    // Create a new alert
    createAlert(type = UnicornAlertTypes.INFO, message, duration = 5000, onClick)
    {
        let alert = document.createElement('div');
        alert.classList.add('unicorn_alert');
        alert.innerHTML = message;

        if(onClick == undefined)
        {
            alert.addEventListener('click', () => this.removeAlert(alert));
        }
        else
        {
            alert.addEventListener('click', onClick);
        }

        switch(type)
        {
            case UnicornAlertTypes.SUCCESS:
                alert.style.borderLeftColor = UnicornAlertColors.BORDER_SUCCESS;
                alert.style.backgroundColor = UnicornAlertColors.BACKGROUND_SUCCESS;
                alert.style.color = UnicornAlertColors.TEXT_SUCCESS;
                break;
            case UnicornAlertTypes.ERROR:
                alert.style.borderLeftColor = UnicornAlertColors.BORDER_ERROR;
                alert.style.backgroundColor = UnicornAlertColors.BACKGROUND_ERROR;
                alert.style.color = UnicornAlertColors.TEXT_ERROR;
                break;
            case UnicornAlertTypes.WARNING:
                alert.style.borderLeftColor = UnicornAlertColors.BORDER_WARNING;
                alert.style.backgroundColor = UnicornAlertColors.BACKGROUND_WARNING;
                alert.style.color = UnicornAlertColors.TEXT_WARNING;
                break;
            case UnicornAlertTypes.INFO:
                alert.style.borderLeftColor = UnicornAlertColors.BORDER_INFO;
                alert.style.backgroundColor = UnicornAlertColors.BACKGROUND_INFO;
                alert.style.color = UnicornAlertColors.TEXT_INFO;
                break;
            default:
                alert.style.borderLeftColor = UnicornAlertColors.BORDER_INFO;
                alert.style.backgroundColor = UnicornAlertColors.BACKGROUND_INFO;
                alert.style.color = UnicornAlertColors.TEXT_INFO;
                break;
        }

        this.frame.appendChild(alert);
        this.alerts.push(alert);
        setTimeout(() => this.removeAlert(alert), duration);
    }

    // Remove alert
    removeAlert(alert)
    {
        try
        {
            alert.classList.add('hidden');
            setTimeout(() => {
                alert.remove();
            }, 400);
        }
        catch(e)
        {
            // Alert already removed 
        }
    }


    // Create alert frame
    createFrame()
    {
        let frame = document.createElement('div');
        frame.classList.add('unicorn_alert_frame');
        document.body.appendChild(frame);
        return frame;
    }


    // Implements the basic styles for the alert
    implementStyles()
    {
        let style = document.createElement('style');
        style.innerHTML = `
            .unicorn_alert_frame
            {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1000000;
                pointer-events: none;

                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 1rem;

                padding: 2rem 5%;
            }

            .unicorn_alert
            {
                padding: 1rem 2rem;
                border-radius: 0.25rem;
                border: none;
                font-size: 1rem;
                min-width: 300px;
                width: 80%;
                max-width: 800px;
                position: relative;
                border-left: 1rem solid rgba(0, 0, 0, 0.8);
                animation: unicorn_slide_down 0.5s ease-in-out;
                cursor: pointer;
                pointer-events: all;
                
                transition: opacity 0.5s;
            }

            .unicorn_alert.hidden
            {
                opacity: 0;
                pointer-events: none;
            }

            @keyframes unicorn_slide_down
            {
                0%
                {
                    transform: translateY(-200%);
                    opacity: 0;
                }
                100%
                {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    }
}