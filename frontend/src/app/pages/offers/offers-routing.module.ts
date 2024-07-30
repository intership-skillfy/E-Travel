import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ExursionsPageComponent } from "./exursions-page/exursions-page.component";
import { HikingComponent } from "./hiking/hiking.component";
import { OmraComponent } from "./omra/omra.component";
import { TripsComponent } from "./trips/trips.component";
import { OffersComponent } from './offers.component';

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
        path: 'exursions',
        component: ExursionsPageComponent,
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
