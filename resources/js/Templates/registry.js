import WebsiteClassic from './Website/Classic.vue';
import WebsiteModern from './Website/Modern.vue';
import WebsiteFestive from './Website/Festive.vue';
import WebsiteBotanical from './Website/Botanical.vue';
import WebsiteMidnight from './Website/Midnight.vue';
import WebsiteStorybook from './Website/Storybook.vue';

import InvitationElegant from './Invitation/Elegant.vue';
import InvitationFloral from './Invitation/Floral.vue';
import InvitationBold from './Invitation/Bold.vue';
import InvitationMinimalist from './Invitation/Minimalist.vue';
import InvitationDeco from './Invitation/Deco.vue';
import InvitationConfetti from './Invitation/Confetti.vue';

const websiteTemplates = {
    classic: WebsiteClassic,
    modern: WebsiteModern,
    festive: WebsiteFestive,
    botanical: WebsiteBotanical,
    midnight: WebsiteMidnight,
    storybook: WebsiteStorybook,
};

const invitationTemplates = {
    elegant: InvitationElegant,
    floral: InvitationFloral,
    bold: InvitationBold,
    minimalist: InvitationMinimalist,
    deco: InvitationDeco,
    confetti: InvitationConfetti,
};

export function resolveWebsiteTemplate(key) {
    return websiteTemplates[key] ?? WebsiteClassic;
}

export function resolveInvitationTemplate(key) {
    return invitationTemplates[key] ?? InvitationElegant;
}
