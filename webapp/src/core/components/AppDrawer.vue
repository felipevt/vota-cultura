<template>
  <v-navigation-drawer
    :value="showDrawer"
    class="app--drawer"
    :mini-variant.sync="mini"
    app
    clipped
    :width="drawWidth"
    @input="$emit('input', $event)"
  >
    <vue-perfect-scrollbar
      class="drawer-menu--scroll"
      :settings="scrollSettings"
    >
      <v-list
        dense
        expand
      >
        <template v-for="item in menus">
          <!--group with subitems-->
          <v-list-group
            v-if="item.items"
            :key="item.title"
            :group="item.group"
            :prepend-icon="item.icon"
            no-action="no-action"
          >
            <v-list-tile
              slot="activator"
              ripple="ripple"
            >
              <v-list-tile-content>
                <v-list-tile-title>{{ item.title }}</v-list-tile-title>
              </v-list-tile-content>
            </v-list-tile>
            <template v-for="subItem in item.items">
              <!--sub group-->
              <v-list-group
                v-if="subItem.items"
                :key="subItem.name"
                :group="subItem.group"
                sub-group="sub-group"
              >
                <v-list-tile
                  slot="activator"
                  ripple="ripple"
                >
                  <v-list-tile-content>
                    <v-list-tile-title>{{ subItem.title }}</v-list-tile-title>
                  </v-list-tile-content>
                </v-list-tile>
                <v-list-tile
                  v-for="grand in subItem.children"
                  :key="grand.name"
                  :to="genChildTarget(item, grand)"
                  :href="grand.href"
                  ripple="ripple"
                >
                  <v-list-tile-content>
                    <v-list-tile-title>{{ grand.title }}</v-list-tile-title>
                  </v-list-tile-content>
                </v-list-tile>
              </v-list-group>
              <!--child item-->
              <v-list-tile
                v-else
                :key="subItem.name"
                :to="genChildTarget(item, subItem)"
                :href="subItem.href"
                :disabled="subItem.disabled"
                :target="subItem.target"
                ripple="ripple"
              >
                <v-list-tile-content>
                  <v-list-tile-title>
                    <span>{{ subItem.title }}</span>
                  </v-list-tile-title>
                </v-list-tile-content>
                <v-list-tile-action v-if="subItem.action">
                  <v-icon :class="[subItem.actionClass || 'success--text']">
                    {{ subItem.action }}
                  </v-icon>
                </v-list-tile-action>
              </v-list-tile>
            </template>
          </v-list-group>
          <v-subheader
            v-else-if="item.header"
            :key="item.name"
          >
            {{ item.header }}
          </v-subheader>
          <v-divider
            v-else-if="item.divider"
            :key="item.name"
          />
          <!--top-level link-->
          <v-list-tile
            v-else
            :key="item.name"
            :to="!item.href ? { name: item.name } : null"
            :href="item.href"
            ripple="ripple"
            :disabled="item.disabled"
            :target="item.target"
            rel="noopener"
          >
            <v-list-tile-action v-if="item.icon">
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
              <v-list-tile-title>{{ item.title }}</v-list-tile-title>
            </v-list-tile-content>
            <v-list-tile-action v-if="item.subAction">
              <v-icon class="success--text">
                {{ item.subAction }}
              </v-icon>
            </v-list-tile-action>
          </v-list-tile>
        </template>
      </v-list>
    </vue-perfect-scrollbar>
  </v-navigation-drawer>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';
import VuePerfectScrollbar from 'vue-perfect-scrollbar';
import menuAPI from '@/core/api/menu';

export default {
  name: 'AppDrawer',
  components: {
    VuePerfectScrollbar,
  },
  props: {
    value: {
      type: Boolean,
      default: true,
    },
    expanded: {
      type: Boolean,
      default: true,
    },
    drawWidth: {
      type: [Number, String],
      default: '260',
    },
  },
  data() {
    return {
      mini: false,
      menus: [],
      scrollSettings: {
        maxScrollbarLength: 160,
      },
      showDrawer: false,
      menuInscrivaseAtivo: false,
    };
  },
  computed: {
    ...mapGetters({
      ativarInscricaoConselho: 'cronograma/ativarInscricaoConselho',
      ativarInscricaoOrganizacao: 'cronograma/ativarInscricaoOrganizacao',
      ativarInscricaoEleitor: 'cronograma/ativarInscricaoEleitor',
    }),
    computeGroupActive() {
      return true;
    },
    sideToolbarColor() {
      return this.$vuetify.options.extra.sideNav;
    },
  },
  watch: {
    value(val) {
      this.showDrawer = val;
    },
    ativarInscricaoConselho(value) {
      let objetoMenu = {};
      if (value === true) {
        objetoMenu = {
          title: 'Conselhos de Cultura',
          group: 'apps',
          name: 'Conselho',
          icon: 'group',
        };
      }
      this.definirItemMenuInscricao('Conselho', objetoMenu);
    },
    ativarInscricaoOrganizacao(value) {
      let objetoMenu = {};
      if (value === true) {
        objetoMenu = {
          title: 'Organização ou Entidade Cultural',
          group: 'apps',
          name: 'Organizacao',
          icon: 'color_lens',
        };
      }
      this.definirItemMenuInscricao('Organizacao', objetoMenu);
    },
    ativarInscricaoEleitor(value) {
      let objetoMenu = {};
      if (value === true) {
        objetoMenu = {
          title: 'Eleitor',
          group: 'apps',
          name: 'Eleitor',
          icon: 'thumbs_up_down',
        };
      }
      this.definirItemMenuInscricao('Eleitor', objetoMenu);
    },
  },
  mounted() {
    this.obterCronogramas();
    this.menus = menuAPI;
  },
  methods: {
    ...mapActions({
      obterCronogramas: 'cronograma/obterCronogramas',
    }),

    definirItemMenuInscricao(nomeMenu, objetoMenu) {
      const nomeAgrupadorInscricoes = 'AgrupadorInscricao';
      const indiceAgrupadorInscricaoDeMenus = this.menus.findIndex(indice => indice.name === nomeAgrupadorInscricoes);
      if (indiceAgrupadorInscricaoDeMenus === -1) {
        this.menus.push({ header: 'Inscreva-se', name: nomeAgrupadorInscricoes });
      }
      const indiceItemDeMenus = this.menus.findIndex(indice => indice.name === nomeMenu);
      if (Object.keys(objetoMenu).length < 1 && indiceItemDeMenus > -1) {
        this.menus.splice(indiceItemDeMenus);
      }
      if (Object.keys(objetoMenu).length > 1 && indiceItemDeMenus === -1) {
        this.menus.push(objetoMenu);
      }
    },

    genChildTarget(item, subItem) {
      if (subItem.href) {
        return {};
      }
      let child = { name: `${item.group}/${subItem.name}` };
      if (subItem.component) {
        child = { name: subItem.component };
      }
      return child;
    },
  },
};
</script>

<style lang="stylus" scoped>

</style>
