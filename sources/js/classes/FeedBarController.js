class FeedBarController
{

    feedBar;

    constructor()
    {
        this.feedBar = document.createElement('div');
        this.feedBar.classList.add('feed-bar');

        this.createActionButton('media/icons/icon_dark_chat.svg', 'Feed', () => this.redirect('index.html'));
        this.createActionButton('media/icons/icon_dark_map.svg', 'Map', () => this.redirect('maps.html'));
        this.createActionButton('media/icons/icon_dark_add.svg', 'Post', () => this.redirect('post.html'));
        this.createActionButton('media/icons/icon_dark_friends.svg', 'Friends', () => {});
        this.createActionButton('media/icons/icon_dark_profile.svg', 'Profile', () => {});

        this.render();
    }

    createActionButton(icon, name, onClick)
    {
        let button = document.createElement('button');
        let image = document.createElement('img');
        image.src = icon;
        image.alt = name;
        button.appendChild(image);
        button.onclick = onClick;
        this.feedBar.appendChild(button);
    }

    redirect(url)
    {
        window.location.href = url;
    }

    render()
    {
        document.querySelector('main').appendChild(this.feedBar); 
    }
}

document.addEventListener('DOMContentLoaded', () => {   
    new FeedBarController();
});