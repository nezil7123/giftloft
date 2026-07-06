import WebsiteClassic from './Website/Classic.vue';
import WebsiteModern from './Website/Modern.vue';
import WebsiteFestive from './Website/Festive.vue';
import WebsiteBotanical from './Website/Botanical.vue';
import WebsiteMidnight from './Website/Midnight.vue';
import WebsiteStorybook from './Website/Storybook.vue';
import WebsiteAurora from './Website/Aurora.vue';
import WebsiteCinema from './Website/Cinema.vue';
import WebsiteRoyal from './Website/Royal.vue';
import WebsitePrism from './Website/Prism.vue';
import WebsiteEmber from './Website/Ember.vue';
import WebsiteNova from './Website/Nova.vue';

import InvitationElegant from './Invitation/Elegant.vue';
import InvitationFloral from './Invitation/Floral.vue';
import InvitationBold from './Invitation/Bold.vue';
import InvitationMinimalist from './Invitation/Minimalist.vue';
import InvitationDeco from './Invitation/Deco.vue';
import InvitationConfetti from './Invitation/Confetti.vue';
import InvitationAurora from './Invitation/Aurora.vue';
import InvitationRoyal from './Invitation/Royal.vue';
import InvitationNoir from './Invitation/Noir.vue';
import InvitationVintage from './Invitation/Vintage.vue';
import InvitationNeon from './Invitation/Neon.vue';

const websiteTemplates = {
    classic: WebsiteClassic,
    modern: WebsiteModern,
    festive: WebsiteFestive,
    botanical: WebsiteBotanical,
    midnight: WebsiteMidnight,
    storybook: WebsiteStorybook,
    aurora: WebsiteAurora,
    cinema: WebsiteCinema,
    royal: WebsiteRoyal,
    prism: WebsitePrism,
    ember: WebsiteEmber,
    nova: WebsiteNova,
};

const invitationTemplates = {
    elegant: InvitationElegant,
    floral: InvitationFloral,
    bold: InvitationBold,
    minimalist: InvitationMinimalist,
    deco: InvitationDeco,
    confetti: InvitationConfetti,
    aurora: InvitationAurora,
    royal: InvitationRoyal,
    noir: InvitationNoir,
    vintage: InvitationVintage,
    neon: InvitationNeon,
};

export function resolveWebsiteTemplate(key) {
    return websiteTemplates[key] ?? WebsiteClassic;
}

export function resolveInvitationTemplate(key) {
    return invitationTemplates[key] ?? InvitationElegant;
}
