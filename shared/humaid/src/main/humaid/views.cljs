(ns humaid.views
  (:require
   [clojure.string :as string]
   [goog.string :as gstring]
   [goog.string.format]
   [humaid.date :as date]
   [humaid.config :refer [MKS-ADDR]]
   [reagent.core :as r]
   [reitit.frontend.easy :as rfe]
   [re-frame.core :refer [subscribe dispatch]]
   [reagent-keybindings.keyboard :as kb]
   ))

(defn client-fullname [client]
  (str (:lastname client) " " (:firstname client) " " (:middlename client)))

(defn client-photo [photo-name]
  (when photo-name
    (str MKS-ADDR "/uploads/images/client/photo/" (subs photo-name 0 2) "/" photo-name)))

(defn href
  "Return relative url for given route. Url can be used in HTML links."
  ([k]
   (href k nil nil))
  ([k params]
   (href k params nil))
  ([k params query]
   (rfe/href k params query)))

(defn stopwatch []
  (let [v @(subscribe [:stopwatch-val])
        min  (quot v 60)
        sec  (rem v 60)]
    [:div
     {:class ["is-size-2" (when (> min 4) "has-text-warning")]}
     (gstring/format "%02d:%02d" min sec)]))

(defn notification []
  (let [s @(subscribe [:notification])]
    (when-not (empty? s)
      [:div {:class ["notification" (str "is-" (name (:kind s)))]}
       [:button.delete {:on-click #(dispatch [:clear-notification])}]
       (:msg s)])))

(defn modal-loading []
  [:div.modal.is-active
   [:div.modal-backgroud]
   [:div.modal-content
    [:div.content "Загружается..."]
    [:button.button.is-large.is-loading " -----------"]]])

(defn search-page []
  [:section.section>div.container>div.content
   [:section.columns
    [:div.column.is-2
     [stopwatch]]

    (let [clients @(subscribe [:search])
          loading @(subscribe [:loading])
          not-found (= clients [])]

      [:div.column
       [:h3 "Поиск клиента"]
       [:div {:class ["control" (when (:search loading) "is-loading")]}
        [:input
         {:class ["input" "is-large" (when not-found "is-danger")]
          :type "text"
          :placeholder "ФИО..."
          :on-change #(dispatch [:client-search (-> % .-target .-value)])
          }]
        (when not-found [:p.help.is-danger "Не нашли =("])]

       (for [{:keys [id birthDate] :as client} clients
             :let [birth-date (date/date->yy-mm-dd (js/Date. birthDate))]]
         ^{:key id}
         [:div.is-size-2
          [:a {:href (href :client {:client-id id})}
           (str (client-fullname client) " (" birth-date ")")]]
         )])]])

(defn client-page []
  (let [loading @(subscribe [:loading])
        client @(subscribe [:client])
        client-id (:id client)
        delivery-params (fn [kind] [:delivery
                                    {:client-id client-id
                                     :delivery-items-kind kind}])]
    (if (:client loading)

      [modal-loading]

      [:section.section.container.content
       [:section.columns
        [:div.column.is-2
         [stopwatch]
         [:button.button {:on-click #(dispatch [:push-state :search])} "Завершить выдачу"]]

          [:div.column.is-3
           [:p>a {:href (apply href (delivery-params :clothes))}
            [:button.button.is-large.is-block "1. Одежда"]]
           [:p>a {:href (apply href (delivery-params :hygiene))}
            [:button.button.is-large.is-block "2. Гигиена"]]
           [:p>a {:href (apply href (delivery-params :crutches))}
            [:button.button.is-large.is-block "3. Костыли/трости"]]

           [kb/kb-action "1" #(dispatch (into [:push-state] (delivery-params :clothes)))]
           [kb/kb-action "2" #(dispatch (into [:push-state] (delivery-params :hygiene)))]
           [kb/kb-action "3" #(dispatch (into [:push-state] (delivery-params :crutches)))]]

        [:div.column.is-3
         (if-let [photo-src (client-photo (:photo_name client))]
           [:img {:src photo-src}]
           [:p "Без фото"])
         [:p.is-size-5 (client-fullname client)]]]]
      )))

(defn redirect-modal [state]
  [:div.modal {:class [(when (:active? @state) "is-active")]}
   [:div.modal-background]
   [:div.modal-card
    [:div.modal-card-head]
    [:div.modal-card-body
     [:div.content "Есть несохранённые изменения. Продолжить?"]]
    [:div.modal-card-foot
     [:button.button {:on-click (:redirect-fn @state)} "Да"]
     [:button.button {:on-click #(swap! state assoc :active? false) :aria-label "close"} "Нет"]]]])

(defn delivery-page []
  (r/with-let [
               hotkeys "1234567890qwertyuiopasdfghjklzxcvbnm[];',./"
               kind (keyword (:delivery-items-kind @(subscribe [:path-params])))

               category-id (kind {:clothes 3
                                  :hygiene 17
                                  :crutches 22})
               heading (str "Выдача " (kind {:clothes "одежды"
                                             :hygiene "предметов гигиены"
                                             :crutches "костылей и тростей"}))

               selected-items (r/atom #{})

               switch-selected! (fn [selected-items item-id]
                                  (if (contains? @selected-items item-id)
                                    (swap! selected-items disj item-id)
                                    (swap! selected-items conj item-id)))

               redirect-modal-state (r/atom {:active? false})
               redirect! (fn [selected-items page & params]
                           (let [redirect-fn #(dispatch (-> (concat [:push-state page] params) vec))]
                             (if (empty? @selected-items)
                               (redirect-fn)
                               (swap! redirect-modal-state assoc
                                      :active? true
                                      :redirect-fn redirect-fn))))

               delivery-unavailable-until
               (fn
                 ;; deliveries -- list of items delivered to a client (list of maps with :delivery_item_id and :delivered_at keys).
                 ;; When current item (2nd arg) cannot be issued today returns nearest available date.
                 [deliveries {item-id :id limit-days :limit_days}]
                 (when-let [item-delivery (some
                                           #(when (= (str item-id) (:delivery_item_id %)) %)
                                           deliveries)]
                   (let [date (js/Date. (:delivered_at item-delivery))
                         next-available-date (date/add-days  date limit-days)]
                     (when (< (date/today) next-available-date)
                       next-available-date))))]

    (let [loading @(subscribe [:loading])
          client @(subscribe [:client])
          client-deliveries @(subscribe [:client-deliveries])
          delivery-items @(subscribe [:delivery-items])]

      (if (:client loading)
        [modal-loading]

        [:section.section.container.content

         [redirect-modal redirect-modal-state]

         [:section.columns
          [:div.column.is-2
           [stopwatch]
           [:p>button.button
            {:on-click #(redirect! selected-items :client {:client-id (:id client)})}
            "Вернуться к списку"]
           [:p>button.button.is-light.is-danger
            {:on-click #(redirect! selected-items :search)}
            "Завершить выдачу"]]

          [:section.column
           (let [selected @selected-items
                 items (filter #(= category-id (:category %)) delivery-items)]
             [:div.container.content
              [:h3 heading]
              [:p.is-size-5.has-text-weight-light (str " " (:name client))]
              [:section

               (for [[{:keys [id name] :as item} key] (map vector items (concat hotkeys (repeat nil)))
                     :let [unavailable-until (delivery-unavailable-until client-deliveries item)
                           selected? (contains? selected id)]]
                 [:<> {:key id}
                  [:button
                   {:class ["button" "mx-2" "my-2"
                            (when selected? "is-success")
                            (when (and unavailable-until (not selected?)) "is-dark")]
                    :on-click #(switch-selected! selected-items id)}

                   (str (when key (str key ". ")) (string/capitalize name))
                   (when unavailable-until
                     [:span.has-text-weight-light.is-size-6.mx-1
                      (str "(доступно с " (date/date->mm-dd unavailable-until) ")")])]
                  (when key
                    [kb/kb-action key #(switch-selected! selected-items id)])

                  ])]])

           (when-let [services
                      (seq
                       (filter
                        #(= (str category-id) (:type %))
                        @(subscribe [:client-services])))]
             [:div.container.content
              [:h5 "Выдачи-услуги"]
              [:ul
               (for [{comment :comment created-at :created_at} services
                     :let [created-at (date/date->yy-mm-dd (js/Date. created-at))]]
                 [:li (str comment " (" created-at ")")])]])

           [:div.container.content
            [:button.button.is-dark.is-primary
             {:disabled (empty? @selected-items)
              :on-click #(dispatch [:save-deliveries (:id client) @selected-items])}
             "Сохранить"]
            [kb/kb-action "ctrl-enter" #(dispatch [:save-deliveries (:id client) @selected-items])]
            ]]]]
        ))))

(defn not-found-page []
  [:section.section>div.container>div.content
   [:p "404 (страница не найдена)"]
   [:p  [:a {:href (href :search)} "назад"]]])

(defn page []
  (let [current-route @(subscribe [:current-route])]
    (if current-route
      [:div
       [kb/keyboard-listener]
       [(-> current-route :data :view)]]
      [not-found-page])))
