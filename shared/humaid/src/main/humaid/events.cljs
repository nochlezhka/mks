(ns humaid.events
  (:require
   [ajax.core :refer [json-request-format json-response-format]]
   [day8.re-frame.http-fx]
   [clojure.string :as string]
   [re-frame.core :refer [reg-event-db reg-event-fx reg-fx dispatch]]
   [reitit.frontend.easy :as rfe]
   [reitit.frontend.controllers :as rfc]
   [humaid.config :refer [API-ADDR]]))

;; Routing
;; --------------------------------------

(reg-event-fx
 :push-state
 (fn [_ [_ & route]]
   {:push-state route}))

(reg-event-db
 :navigated
 (fn [db [_ new-match]]
   (let [old-match   (:current-route db)
         controllers (rfc/apply-controllers (:controllers old-match) new-match)]
     (assoc db :current-route (assoc new-match :controllers controllers)))))

(reg-fx
 :push-state
 (fn [route]
   (apply rfe/push-state route)))

;; Notification
;; --------------------------------------

(defonce timeouts (atom {}))

(reg-fx
 :timeout
 (fn [{:keys [id event time]}]
   (when-some [existing (get @timeouts id)]
     (js/clearTimeout existing)
     (swap! timeouts dissoc id))
   (when (some? event)
     (swap! timeouts assoc id
            (js/setTimeout
             (fn []
               (dispatch event))
             time)))))

(reg-event-fx
 :show-notification
 (fn [{:keys [db]} [_ kind msg]]
   {:db (assoc db :notification {:kind kind :msg msg})
    :timeout {:id :notification
              :event [:clear-notification]
              :time 4200}}))

(reg-event-db
 :clear-notification
 (fn [db]
   (dissoc db :notification)))

;; Stopwatch
;; --------------------------------------

(reg-event-fx
 :start-stopwatch
 (fn [{:keys [db]} _]
   (when-not (-> db :stopwatch :started)
     {:db (assoc-in db [:stopwatch :started] true)
      :start-stopwatch nil})))

(reg-event-db
 :inc-stopwatch
 (fn [db]
   (update-in db [:stopwatch :val] inc)))

(reg-event-fx
 :reset-stopwatch
 (fn [{:keys [db]} _]
   {:db (assoc db :stopwatch {:started false :val 0})
    :stop-stopwatch nil}))

(defonce stopwatch-fn (atom nil))

(reg-fx
 :start-stopwatch
 (fn []
   (reset! stopwatch-fn
           (js/setInterval #(dispatch [:inc-stopwatch]) 1000))))

(reg-fx
 :stop-stopwatch
 (fn []
   (js/clearInterval @stopwatch-fn)))

;; API calls
;; --------------------------------------

(defn set-loading [db key val]
  (assoc-in db [:loading key] val))

(reg-event-fx
 :client-search
 (fn [{:keys [db]} [_ search-str]]
   (let [search-str (string/trim search-str)]
     (if (> (count search-str) 2)
       {:http-xhrio {:method :get
                     :uri (str API-ADDR "/clients/search")
                     :params {:v search-str}
                     :response-format (json-response-format {:keywords? true})
                     :on-success [:api-request-success :search]
                     :on-failure [:api-request-error :search]}
        :db (set-loading db :search true)
        :dispatch [:start-stopwatch]
        }
       {:db (dissoc db :search)
        :dispatch [:reset-stopwatch]}))))

(reg-event-fx
 :client-get
 (fn [{:keys [db]} [_ client-id]]
   (when (or (empty? (:client db))
             (not= (str (get-in db [:client :id])) client-id))
     {:db (set-loading db :client true)
      :http-xhrio {:method :get
                   :uri (str API-ADDR "/clients/" client-id)
                   :response-format (json-response-format {:keywords? true})
                   :on-success [:api-request-success :client]
                   :on-failure [:client-get-failure]}
      })))

(reg-event-fx
 :client-get-failure
 (fn [{:keys [db]} [_ resp]]
   {:db (-> db
            (set-loading :client false)
            (assoc-in [:search] []))

    :dispatch-n [[:push-state :search]
                 (if (= 404 (:status resp))
                   [:show-notification :danger "Клиент не найден"]
                   [:api-request-error :client resp])]}))

(reg-event-fx
 :client-get-deliveries
 (fn [{:keys [db]} [_ client-id]]
   {:db (set-loading db :client-deliveries true)
    :http-xhrio {:method :get
                 :uri (str API-ADDR "/clients/" client-id "/deliveries")
                 :response-format (json-response-format {:keywords? true})
                 :on-success [:api-request-success :client-deliveries]
                 :on-failure [:api-request-error :client-deliveries]}}))

(reg-event-fx
 :delivery-items-get
 (fn [{:keys [db]} [_  branch-id]]
   (let [branch-id (js/parseInt branch-id)]
     {:db (set-loading db :delivery-items true)
      :http-xhrio {:method :get
                   :uri (str API-ADDR "/delivery_items")
                   :params {:b branch-id}
                   :response-format (json-response-format {:keywords? true})
                   :on-success [:api-request-success :delivery-items]
                   :on-failure [:api-request-error :delivery-items]}})))

(def delivery-item-categories {3 "Одежда"
                               17 "Гигиена"
                               22 "Аксессуары"
                               39 "Костыли/трости"
                               45 "Посуда"
                               46 "Подарок"
                               })

(reg-event-fx
 :client-get-services
 (fn [{:keys [db]} [_ client-id]]
   {:db (set-loading db :client-services true)
    :http-xhrio {:method :get
                 :uri (str API-ADDR "/clients/" client-id "/services")
                 :params {:types (keys delivery-item-categories)}
                 :vec-strategy :rails
                 :response-format (json-response-format {:keywords? true})
                 :on-success [:api-request-success :client-services]
                 :on-failure [:api-request-error :client-services]}}))

(reg-event-fx
 :save-deliveries
 (fn [{:keys [db]} [_ client-id item-ids]]
   {:db (set-loading db [:save-deliveries] true)
    :http-xhrio {:method :post
                 :uri (str API-ADDR "/clients/" client-id "/deliveries")
                 :format (json-request-format {:keywords? true})
                 :response-format (json-response-format {:keywords? true})
                 :params {"item_ids" item-ids}
                 :on-success [:save-deliveries-success client-id]
                 :on-failure [:api-request-error :save-deliveries]}}))

(reg-event-fx
 :save-deliveries-success
 (fn [{:keys [db]} [_ client-id _]]
   {:db (-> db
            (set-loading :save-deliveries false))
    :dispatch-n [[:show-notification :success "Выдача сохранена!"]
                 [:push-state :client {:client-id client-id}]]}))

(reg-event-db
 :api-request-success
 (fn [db [_ request-name resp]]
   (-> db
       (set-loading request-name false)
       (assoc request-name resp))))

(reg-event-fx
 :api-request-error
 (fn [db [_ request-type response]]
   {:db (-> db
            (assoc-in [:errors request-type] (get-in response [:response :errors]))
            (set-loading request-type false))

    :dispatch [:show-notification :danger
               (str "Ошибка при запросе к серверу: " request-type)]}))

(reg-event-db
 :clear-search
 (fn [db _]
   (dissoc db :search)))

(reg-event-fx
 :branches-get
 (fn [{:keys [db]} _]
   {:db (set-loading db :branch-items true)
    :http-xhrio {:method :get
                 :uri (str API-ADDR "/branches")
                 :response-format (json-response-format {:keywords? true})
                 :on-success [:api-request-success :branch-items]
                 :on-failure [:api-request-error :branch-items]}}))