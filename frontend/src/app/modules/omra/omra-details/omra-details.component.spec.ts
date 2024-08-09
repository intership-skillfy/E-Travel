import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OmraDetailsComponent } from './omra-details.component';

describe('OmraDetailsComponent', () => {
  let component: OmraDetailsComponent;
  let fixture: ComponentFixture<OmraDetailsComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [OmraDetailsComponent]
    });
    fixture = TestBed.createComponent(OmraDetailsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
