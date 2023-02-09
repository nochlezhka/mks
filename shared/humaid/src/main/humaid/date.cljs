(ns humaid.date)

(defn today []
  (doto (js/Date.) (. setHours 0 0 0 0)))

(defn add-days
  ;; Adds number of days to the js date object.
  ;; Months will roll over ( https://codewithhugo.com/add-date-days-js/ )
  [date days]
  (doto (js/Date. date) (. setDate (+ days (. date getDate)))))

(defn date->mm-dd [date]
  (. (js/Intl.DateTimeFormat "default" (js-obj "month" "numeric" "day" "numeric"))
     format date))

(defn date->yy-mm-dd [date]
  (. (js/Intl.DateTimeFormat "default" (js-obj "year" "numeric" "month" "numeric" "day" "numeric"))
     format date))
