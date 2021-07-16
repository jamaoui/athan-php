export default {

    dateDiff(d1, d2) {
        let t2 = d2.getTime();
        let t1 = d1.getTime();
        return t2 - t1;
    },
    inDays: function (d1, d2) {
        return parseInt(this.dateDiff(d1, d2) / (24 * 3600 * 1000)).toLocaleString('en-US', {
            minimumIntegerDigits: 2,
            useGrouping: false
        });
    },
    inHours: function (d1, d2) {
        return parseInt(this.dateDiff(d1, d2) / 1000 / 3600).toLocaleString('en-US', {
            minimumIntegerDigits: 2,
            useGrouping: false
        });
    },
    inMinutes(d1, d2) {
        return parseInt(this.dateDiff(d1, d2) / 1000 / 60).toLocaleString('en-US', {
            minimumIntegerDigits: 2,
            useGrouping: false
        });
    },
    inWeeks: function (d1, d2) {
        return parseInt(this.dateDiff(d1, d2) / (24 * 3600 * 1000 * 7)).toLocaleString('en-US', {
            minimumIntegerDigits: 2,
            useGrouping: false
        });
    },
    inMonths: function (d1, d2) {
        let d1Y = d1.getFullYear();
        let d2Y = d2.getFullYear();
        let d1M = d1.getMonth();
        let d2M = d2.getMonth();

        return (d2M + 12 * d2Y) - (d1M + 12 * d1Y);
    },

    inYears: function (d1, d2) {
        return d2.getFullYear() - d1.getFullYear();
    }
}