class TwoFactorController
{

    display = null;

    displayTwoFactorView(userID, callback)
    {
        if(this.display != null) removeTwoFactorView();
        this.display = document.createElement("div");
        this.display.className = "twofa";

        var form = document.createElement("div");
        form.classList.add("form");

        form.innerHTML = `
            <h2>2-Faktor Authentifizierung</h2>
            <p>Sie haben 2FA aktiviert. Geben Sie den TOTP ein.</p>

            <input type="text" name="input_login_totp" id="input_login_totp" placeholder="123 345">`;

        var button = document.createElement("button");
        button.innerHTML = "Bestätigen";
        button.addEventListener("click", () => {
            this.checkTOTP(userID, callback);
        });

        form.appendChild(button);
        this.display.appendChild(form);
        document.body.appendChild(this.display);
        document.getElementById("input_login_totp").focus();
    }


    checkTOTP(userID, callback)
    {
        var totp = document.getElementById("input_login_totp").value;

        fetch(API_URL + "?endpoint_id=twofactor&totp=" + totp + "&user_id=" + userID, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": profileManager.getAuthToken()
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === "success")
            {
                console.log(data);
                if(!data.result)
                {
                    const unicornManager =  new UnicornAlertHandler();
                    unicornManager.createAlert(UnicornAlertTypes.ERROR, "Der TOTP ist falsch", 5000);
                }
                callback(totp, data.result);
            }
            else
            {
                console.error(data.message);
            }
        });
    }


    removeTwoFactorView()
    {
        if(this.display == null) return;
        this.display.remove();
        this.display = null;
    }
}

var twoFactorController;
document.addEventListener("DOMContentLoaded", () => {
    twoFactorController = new TwoFactorController();
});