import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ExcursionsPageComponent } from "./excursions-page/excursions-page.component";
import { HikingComponent } from "./hiking/hiking.component";
import { TripsComponent } from "./trips/trips.component";
import { OffersComponent } from './offers.component';
import { OmraPageComponent } from './omra-page/omra-page.component';
import { OmraDetailsComponent } from 'src/app/modules/omra/omra-details/omra-details.component';
const routes: Routes = [
  {
    path: '',
    component: OffersComponent,
    children: [
      { path: 'trip', component: TripsComponent },
      { path: 'omra', component: OmraPageComponent },
      { path: 'excursions', component: ExcursionsPageComponent },
      { path: 'offer-details', component: OmraDetailsComponent },
      { path: 'hiking', component: HikingComponent },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class OffersRoutingModule {}
