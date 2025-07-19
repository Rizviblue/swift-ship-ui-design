import { useState } from "react";
import { Layout } from "@/components/courier/Layout";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Badge } from "@/components/ui/badge";
import { Search, Package, MapPin, Clock, CheckCircle, Truck } from "lucide-react";

const TrackCourier = () => {
  const [trackingNumber, setTrackingNumber] = useState("");
  const [trackingResult, setTrackingResult] = useState<any>(null);

  const handleTrack = (e: React.FormEvent) => {
    e.preventDefault();
    
    // Mock tracking data
    const mockData = {
      trackingNumber: trackingNumber,
      status: "in-transit",
      senderName: "John Doe",
      receiverName: "Jane Smith",
      estimatedDelivery: "2024-01-18",
      currentLocation: "Distribution Center - Downtown",
      timeline: [
        {
          status: "Order Placed",
          date: "2024-01-15 10:00 AM",
          location: "Origin",
          completed: true
        },
        {
          status: "Picked Up",
          date: "2024-01-15 02:30 PM",
          location: "Pickup Location",
          completed: true
        },
        {
          status: "In Transit",
          date: "2024-01-16 08:00 AM",
          location: "Distribution Center",
          completed: true
        },
        {
          status: "Out for Delivery",
          date: "2024-01-18 09:00 AM",
          location: "Local Facility",
          completed: false
        },
        {
          status: "Delivered",
          date: "Expected by 6:00 PM",
          location: "Destination",
          completed: false
        }
      ]
    };
    
    setTrackingResult(mockData);
  };

  const getStatusBadge = (status: string) => {
    const statusConfig = {
      "pending": { variant: "secondary", icon: Clock },
      "picked-up": { variant: "default", icon: Package },
      "in-transit": { variant: "default", icon: Truck },
      "delivered": { variant: "default", icon: CheckCircle },
      "cancelled": { variant: "destructive", icon: Clock }
    };
    
    const config = statusConfig[status as keyof typeof statusConfig] || statusConfig.pending;
    const Icon = config.icon;
    
    return (
      <Badge variant={config.variant as any} className="flex items-center gap-1">
        <Icon className="w-3 h-3" />
        {status.replace("-", " ")}
      </Badge>
    );
  };

  return (
    <Layout userRole="customer" userName="Jane Customer" userEmail="jane@email.com">
      <div className="p-6">
        <div className="mb-6">
          <h1 className="text-2xl font-bold text-foreground">Track Your Courier</h1>
          <p className="text-muted-foreground">Enter your tracking number to see delivery status</p>
        </div>

        <Card className="max-w-2xl mx-auto mb-8">
          <CardHeader>
            <CardTitle className="flex items-center">
              <Search className="w-5 h-5 mr-2" />
              Track Package
            </CardTitle>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleTrack} className="space-y-4">
              <div>
                <Label htmlFor="tracking">Tracking Number</Label>
                <Input
                  id="tracking"
                  placeholder="Enter tracking number (e.g., CP12345678)"
                  value={trackingNumber}
                  onChange={(e) => setTrackingNumber(e.target.value)}
                  required
                />
              </div>
              <Button type="submit" className="w-full">
                <Search className="w-4 h-4 mr-2" />
                Track Package
              </Button>
            </form>
          </CardContent>
        </Card>

        {trackingResult && (
          <div className="max-w-4xl mx-auto space-y-6">
            <Card>
              <CardHeader>
                <CardTitle>Package Details</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div className="space-y-4">
                    <div>
                      <p className="text-sm text-muted-foreground">Tracking Number</p>
                      <p className="font-semibold">{trackingResult.trackingNumber}</p>
                    </div>
                    <div>
                      <p className="text-sm text-muted-foreground">Status</p>
                      {getStatusBadge(trackingResult.status)}
                    </div>
                    <div>
                      <p className="text-sm text-muted-foreground">Current Location</p>
                      <p className="font-medium flex items-center">
                        <MapPin className="w-4 h-4 mr-1" />
                        {trackingResult.currentLocation}
                      </p>
                    </div>
                  </div>
                  <div className="space-y-4">
                    <div>
                      <p className="text-sm text-muted-foreground">From</p>
                      <p className="font-medium">{trackingResult.senderName}</p>
                    </div>
                    <div>
                      <p className="text-sm text-muted-foreground">To</p>
                      <p className="font-medium">{trackingResult.receiverName}</p>
                    </div>
                    <div>
                      <p className="text-sm text-muted-foreground">Estimated Delivery</p>
                      <p className="font-medium">{trackingResult.estimatedDelivery}</p>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Delivery Timeline</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {trackingResult.timeline.map((item: any, index: number) => (
                    <div key={index} className="flex items-start space-x-4">
                      <div className={`w-4 h-4 rounded-full border-2 mt-1 ${
                        item.completed 
                          ? 'bg-primary border-primary' 
                          : 'border-muted-foreground bg-background'
                      }`} />
                      <div className="flex-1">
                        <div className="flex items-center justify-between">
                          <p className={`font-medium ${
                            item.completed ? 'text-foreground' : 'text-muted-foreground'
                          }`}>
                            {item.status}
                          </p>
                          <p className="text-sm text-muted-foreground">{item.date}</p>
                        </div>
                        <p className="text-sm text-muted-foreground">{item.location}</p>
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </div>
        )}
      </div>
    </Layout>
  );
};

export default TrackCourier;