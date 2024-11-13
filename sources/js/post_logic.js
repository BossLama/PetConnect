let textarea_description = null;
let button_submit = null;

document.addEventListener('DOMContentLoaded', function() {
    textarea_description = document.getElementById('input_post_text');
    button_submit = document.getElementById('button_submit');

    textarea_description.addEventListener('input', checkCanSubmit);
});

// Check if the description is long enough to submit
function checkCanSubmit()
{
    if(textarea_description.value.length > 10)
    {
        button_submit.disabled = false;
        return true;
    }
    else
    {
        button_submit.disabled = true;
        return false;
    }
}