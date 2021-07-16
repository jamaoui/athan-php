<template>
  <div class="container-fluid athan-container-fluid">
    <div class="container athan-container pt-5">
      <table id="athan-table" border="0" class="table table-responsive table-borderless w-100">
        <tbody>
        <tr>
          <th scope="row">{{ getCurrentSalat.athan }}</th>
          <th>
            <span v-if="getCurrentSalat.remaining.years>0">{{ getCurrentSalat.remaining.years }} ans </span>
            <span v-if="getCurrentSalat.remaining.days>0">{{ getCurrentSalat.remaining.days }} jours </span>
            {{ getCurrentSalat.remaining.hours }} : {{ getCurrentSalat.remaining.minutes }} :
            {{ getSecondesToNextMinute }}
          </th>
        </tr>
        <tr>
          <th scope="row">
            <b-icon icon="sun" class="text-warning" animation="spin" font-scale="2"></b-icon>
          </th>
          <th>
            <b-icon icon="clock" animation="spin" class="text-danger" font-scale="2"></b-icon>
          </th>
        </tr>
        <tr v-for="(value,key) in getTimings" :key="key">
          <td>{{ key }}</td>
          <td>{{ value }}</td>
        </tr>
        </tbody>
      </table>
      <div class="buttons">
        <button v-on:click="decrementDate" class="previous">
          <span class="h3">
          <b-icon icon="arrow-left-circle"></b-icon>
          </span>
          {{ this.previousDate| moment("dddd Do MMMM  YYYY") }}
        </button>
        <button disabled class="current">
          {{ this.currentDate| moment("dddd Do MMMM  YYYY") }}
        </button>
        <button v-on:click="incrementDate" class="next">
          {{ this.nextDate| moment("dddd Do MMMM  YYYY") }}
          <span class="h3">
          <b-icon icon="arrow-right-circle"></b-icon>
          </span>
        </button>
      </div>
    </div>
  </div>
</template>
<script>
import date from "../utils/date";

export default {
  name: 'Athan',
  data() {
    return {
      longitude: null,
      latitude: null,
      // SALAT
      currentSalat: null,
      secondesToNextMinute: null,
      // DATE OBJ
      currentDate: null,
      previousDate: null,
      nextDate: null,
      day: null,
      //TIMING
      month: null,
      year: null,
      errors: [],
      //JSON DATA
      date: null,
      timings: null,
      meta: null
    }
  },
  computed: {
    getTimings() {
      return this.timings;
    },
    getMeta() {
      return this.meta;
    },
    getDate() {
      return this.date;
    },
    getCurrentSalat() {
      return this.currentSalat;
    },
    getSecondesToNextMinute() {
      return this.secondesToNextMinute;
    },
  },
  methods: {
    setSecondesToNextMinute() {
      setInterval(function () {
        let finishTime = 59;
        this.secondesToNextMinute = finishTime - new Date().getSeconds();
        if (this.secondesToNextMinute === finishTime) {
          this.setCurrentSalat();
        }
      }.bind(this), 1000);
    },
    setCurrentSalat() {
      let currentDate = new Date();

      let currentHour = currentDate.getHours();

      let athans = Object.keys(this.timings).filter(function (x) {
        return !['Sunrise', 'Sunset', 'Imsak', 'Midnight'].includes(x);
      })
      let nextSalat = undefined;
      athans.forEach((athan) => {
        let athanTime = this.timings[athan].substr(0, 5).split(':');
        let athanHour = athanTime[0];
        let athanMinute = athanTime[1];
        let remaining = athanHour - currentHour;
        console.log(athan, remaining);
        if (remaining > 0 && nextSalat === undefined) {
          let prevu = new Date(this.year, this.month, this.day, athanHour, athanMinute, 0, 0);
          nextSalat = {
            athan: athan,
            remaining: {
              years: date.inYears(currentDate, prevu),
              days: date.inDays(currentDate, prevu),
              hours: date.inHours(currentDate, prevu),
              minutes: date.inMinutes(currentDate, prevu),
            }
          }
        }
      });
      if (nextSalat === undefined) {
        let athan = 'Fajr';
        let athanTime = this.timings[athan].substr(0, 5).split(':');
        let athanHour = athanTime[0];
        let athanMinute = athanTime[1];
        let remaining = athanHour - currentHour;
        console.log(athan, remaining);
        let prevu = new Date(this.year, this.month, this.day, athanHour, athanMinute, 0, 0);
        nextSalat = {
          athan: athan,
          remaining: {
            years: date.inYears(currentDate, prevu),
            days: date.inDays(currentDate, prevu),
            hours: date.inHours(currentDate, prevu),
            minutes: date.inMinutes(currentDate, prevu),
          }
        }
      }
      this.currentSalat = nextSalat;
    },
    changeDate(nbDays) {
      let date = new Date();
      if (this.year !== null) {
        date.setFullYear(this.year);
      }
      if (this.month !== null) {
        date.setMonth(this.month);
      }
      if (this.day !== null) {
        date.setDate(this.day);
      }
      this.currentDate = date;

      date.setDate(date.getDate() + nbDays);
      let nextDay = new Date(date.getTime());
      nextDay.setDate(nextDay.getDate() + 1)

      let previousDay = new Date(date.getTime());
      previousDay.setDate(previousDay.getDate() - 1)

      this.nextDate = nextDay;
      this.previousDate = previousDay;
      this.year = date.getFullYear();
      this.month = date.getMonth();
      this.day = date.getDate();
    },
    incrementDate() {
      this.changeDate(+1);
      this.setJsonData();
    },
    decrementDate() {
      this.changeDate(-1);
      this.setJsonData();

    },
    async initApp() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
          this.longitude = position.coords.longitude;
          this.latitude = position.coords.latitude;
          this.setJsonData();
        }, () => {
          this.errors.push('la géolocalisation est obligatoire pour utiliser ce service');
        });

      } else {
        this.errors.push('Geolocation is not supported by this browser.');
      }

      return this.errors.length === 0;
    },
    setJsonData() {
      this.$http.get('/api/get?latitude=' + this.latitude + '&month=' + this.month + '&year=' + this.year + '&longitude=' + this.longitude + '&day=' + this.day).then((response) => {
        this.date = response.data.data.date;
        this.meta = response.data.data.meta;
        this.timings = response.data.data.timings;
        this.errors = response.data.errors;
        this.setCurrentSalat();
      }).catch((errors) => {
        /*errors.forEach((error) => {
          this.errors.push(error);
        });*/
      })
    },
    renderErrors() {
      //console.log(this.getErrors());
    }
  },
  async mounted() {
    this.changeDate(0);
    this.initApp().then(() => {
      this.setSecondesToNextMinute()
    });
  }
}
</script>
<style lang="scss">
#app {
  width: 100vw;
}

body {
  background-image: url('../images/0071.001-Mosquee-Hassan-II-02-Ombrages.jpg');
  background-size: cover;
  min-height: 90vh;
  display: flex;
  align-items: center;
}

$background-tr: rgba(0, 0, 0, 0.2);
#athan-table {
  border-radius: 1rem;
  border-collapse: separate;
  border: 10px groove rgba(0, 0, 0, 0.1);
  color: white;
  background-color: rgba(0, 0, 0, 0.4);
  width: 75rem;
  font-size: 1.3rem;
  margin: 0 auto;
  box-shadow: 1px 1px 25px rgba(0, 0, 0, 0.6);
  padding: 1rem;

  tr {
    th {
      background-color: lighten($background-tr, 50);
    }

    td, th {
      text-align: center;
    }

    &:nth-child(odd) {
      background-color: lighten($background-tr, 4);
    }
  }

}

.buttons {
  justify-content: center;
  align-items: center;
  display: flex;

  button {
    border: none;
    background-color: rgba(0, 0, 0, 0.6);
    color: white;
    width: 33.33%;
    border-bottom: 5px solid white;
  }

  .center {
    background-color: rgba(0, 0, 0, 0.9);
    font-size: 1.2rem;
    border-color: white;
  }

  .next {
    float: right;
  }

  .previous {
    float: left;
  }
}
</style>