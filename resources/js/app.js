import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import intersect from '@alpinejs/intersect';
import docsSearch from './docs-search';

Alpine.plugin(collapse);
Alpine.plugin(intersect);
Alpine.data('docsSearch', docsSearch);

window.Alpine = Alpine;
Alpine.start();
