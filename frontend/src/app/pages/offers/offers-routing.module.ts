import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ExcursionsPageComponent } from "./excursions-page/excursions-page.component";
import { HikingComponent } from "./hiking/hiking.component";
import { OmraComponent } from "./omra/omra.component";
import { TripsComponent } from "./trips/trips.component";
import { OffersComponent } from './offers.component';
import { AddOfferComponent } from './forms/add-offer/add-offer.component';
const routes: Routes = [
  {
    path: '',
    component: OffersComponent,
    children: [
      {
        path: 'trips',
        component: TripsComponent,
      },
      {
        path: 'omra',
        component: OmraComponent,
      },
      {
        path: 'excursions',
        component: ExcursionsPageComponent,
      },
      {
        path: 'add-offer/:type',
        component: AddOfferComponent,
      },
      {
        path: 'hiking',
        component: HikingComponent,
      },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class OffersRoutingModule {}
