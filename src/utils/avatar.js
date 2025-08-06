export class AvatarGenerator {
    static styles = [
        'adventurer',
        'adventurer-neutral',
        'avataaars',
        'big-ears',
        'big-ears-neutral',
        'big-smile',
        'bottts',
        'croodles',
        'croodles-neutral',
        'fun-emoji',
        'icons',
        'identicon',
        'initials',
        'lorelei',
        'micah',
        'miniavs',
        'open-peeps',
        'personas',
        'pixel-art',
        'pixel-art-neutral'
    ];

    static getRandomStyle() {
        return this.styles[Math.floor(Math.random() * this.styles.length)];
    }

    static generateAvatar(seed = null, style = null) {
        if (!seed) {
            seed = Math.random().toString(36).substring(2);
        }
        if (!style) {
            style = this.getRandomStyle();
        }
        return `https://api.dicebear.com/7.x/${style}/svg?seed=${seed}`;
    }

    static generateRandomAvatar() {
        return this.generateAvatar();
    }

    static generateAvatarFromUsername(username) {
        return this.generateAvatar(username);
    }
} 