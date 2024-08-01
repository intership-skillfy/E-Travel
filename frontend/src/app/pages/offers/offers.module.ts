// offers.module.ts
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule } from '@angular/forms';
import { NgbTooltipModule } from '@ng-bootstrap/ng-bootstrap';
import { OffersRoutingModule } from './offers-routing.module';
import { OffersComponent } from './offers.component'; 
import { OmraComponent } from './omra/omra.component';
import { TripsComponent } from './trips/trips.component';
import { HikingComponent } from './hiking/hiking.component';
import { ExcursionsPageComponent } from './excursions-page/excursions-page.component';
import { ExcursionsModule } from 'src/app/modules/excursions/excursions.module';
import { WidgetsModule } from "../../_metronic/partials/content/widgets/widgets.module";
import { AddOfferComponent } from './forms/add-offer/add-offer.component';
import { FormsModule } from '@angular/forms';


@NgModule({
  declarations: [
    OffersComponent,
    OmraComponent,
    TripsComponent,
    HikingComponent,
    ExcursionsPageComponent,
    AddOfferComponent
  ],
  imports: [
    CommonModule,
    ReactiveFormsModule,
    NgbTooltipModule,
    OffersRoutingModule,
    ExcursionsModule, 
    WidgetsModule,
    FormsModule
  ],
})
export class OffersModule {}
