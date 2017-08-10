/**
 * Weapon module
 */
/* global $ */
import {MUTATION, ACTION} from '@match/js/match/store/mutation-types'

export default {
  state: {
    modal: false,
    enabled: false,
    loaded: false,
    list: [],
    select: null,
    rotate: 0,
    score: 0,
  },
  mutations: {
    // On first load game
    [MUTATION.LOAD] (state, obj) {
      state.enabled = obj.options.weapon

      // Score
      obj.players.some(function (player) {
        if (player.me) {
          state.score = player.score
        }
        return (typeof player.me !== 'undefined')
      })
    },

    // Set score
    [MUTATION.WEAPON.SET_SCORE] (state, score) {
      state.score = score
    },

    // Toggle modal
    [MUTATION.WEAPON.MODAL] (state, status) {
      if (typeof status !== 'undefined') {
        state.modal = status
      } else {
        state.modal = !state.modal
      }
    },

    // Set weapons list
    [MUTATION.WEAPON.SET_LIST] (state, list) {
      state.list = list.sort(function (o1, o2) {
        return o1.price - o2.price
      })
      state.loaded = true
    },

    // Select weapon
    [MUTATION.WEAPON.SELECT] (state, weapon) {
      if (weapon) {
        state.select = weapon.name
      } else {
        state.select = null
      }
    },

    // Rotate weapon
    [MUTATION.WEAPON.ROTATE] (state) {
      let rotate = state.rotate + 1
      if (rotate > 3) {
        rotate = rotate % 4
      }
      state.rotate = rotate
      state.list.forEach(function (weapon, iW) {
        if (!weapon.rotate) {
          return true
        }

        let grid = weapon.grid

        // Create new grid
        let newGrid = []
        newGrid.length = grid[0].length
        for (let i = 0; i < newGrid.length; i++) {
          newGrid[i] = []
          newGrid[i].length = grid.length
        }

        // Rotate
        for (let i = 0; i < grid.length; i++) {
          for (let j = 0; j < grid[i].length; j++) {
            newGrid[j][grid.length - i - 1] = grid[i][j]
          }
        }

        // Apply
        weapon.grid = newGrid
      })
    },
  },
  actions: {
    // Load weapon
    [ACTION.WEAPON.LOAD] (context, url) {
      return new Promise((resolve, reject) => {
        $.ajax({
          url: url,
          success: resolve,
        })
      })
    },

    // Before shoot : add weapon infos
    [ACTION.BEFORE_SHOOT] (context, obj) {
      return new Promise((resolve, reject) => {
        if (context.state.select) {
          Object.assign(obj, {
            weapon: context.state.select,
            rotate: context.state.rotate,
          })
          context.commit(MUTATION.WEAPON.SELECT)
        }
        resolve(obj)
      })
    },

    // After each rocket
    [ACTION.AFTER_ROCKET] (context, box) {
      // Update score
      if (box.score && box.score[context.rootState.me.position]) {
        context.commit(MUTATION.WEAPON.SET_SCORE, box.score[context.rootState.me.position])
        delete box.score
      }
    },
  },
  getters: {
    // Get weapon
    getWeapon: (state) => (name) => {
      let weapon = state.list.filter(function (weapon) {
        return (weapon.name === name)
      })

      return (weapon.length) ? weapon[0] : null
    },
  },
}
